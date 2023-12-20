<?php

namespace App\Http\Controllers;

use App\Mail\TestResult;
use App\Models\Attempt;
use App\Models\Result;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AttemptController extends Controller
{
    public function question(string $lang, int $pk, int $num = 1)
    {
        $this->setCurrentLocale($lang);

        $attempt = Attempt::findOrFail($pk);

        if (! ($question = Arr::get($attempt->QuestionOrder, $num - 1))) {
            $this->sendTestResultEmail($attempt);

            return to_route('finish', ['pk' => $pk, 'lang' => App::currentLocale()]);
        }

        if (in_array($question['type'], ['single-choice', 'multiple-choice', 'order'])) {
            shuffle($question['answers']);
        }

        return view('questions', [
            'attempt' => $attempt,
            'question' => $question,
            'num' => $num,
            'userAnswer' => $attempt->result()->where('question_id', $question['id'])->first()?->answer,
            'questionCount' => count($attempt->QuestionOrder),
            'percentage' => $attempt->QuestionOrder ? ($num / count($attempt->QuestionOrder)) * 100 : 0,
        ]);
    }

    public function postAnswers(Request $request, string $lang, int $pk, int $num = 1): RedirectResponse
    {
        $this->setCurrentLocale($lang);

        $attempt = Attempt::findOrFail($pk);

        $question = Arr::get($attempt->QuestionOrder, $num - 1);
        if (! $question) {


            return redirect()->route('finish', ['pk' => $pk]);
        }

        if ($question['type'] === 'single-choice') {
            $this->processSingleChoiceQuestion($request, $attempt, $question);

        } elseif ($question['type'] === 'multiple-choice') {

            if ($error = $this->processMultiChoiceQuestion($request, $attempt, $question)) {
                return back()->withErrors([
                    $question['id'] => $error,
                ]);
            }

        } elseif ($question['type'] === 'order') {

            if ($error = $this->processOrderQuestion($request, $attempt, $question)) {
                return back()->withErrors([
                    $question['id'] => $error,
                ]);
            }

        } elseif ($question['type'] === 'image-custom') {

            if ($error = $this->processImageQuestion($request, $attempt, $question)) {
                return back()->withErrors([
                    $question['id'] => $error,
                ]);
            }

        }

        $attempt->update([
            'correct_answer_count' => $attempt->result()->where('is_correct', true)->count()
        ]);

        return redirect()->route('question', ['pk' => $pk, 'num' => $num + 1, 'lang' => App::currentLocale()]);
    }

    public function finish(string $lang, int $pk)
    {
        $this->setCurrentLocale($lang);

        $attempt = Attempt::findOrFail($pk);
        $this->sendTestResultEmail($attempt);
        $questions = $attempt->test->getQuestions(App::currentLocale());

        $percentage = $questions ? round($attempt->correct_answer_count / count($questions) * 100) : 0;

        return view('result', [
            'attempt' => $attempt,
            'percentage' => $percentage,
            'hasImageCustomQuestion' => $attempt->test->hasImageCustomQuestion(),
        ]);
    }

    private function sendTestResultEmail(Attempt $attempt): void
    {
        try {
            Mail::to(config('app.report_email'))
                ->cc(['toavakam@gmail.com', 'vladimir@mariner.tech'])
                ->send(new TestResult($attempt, App::currentLocale()));
        } catch (\Exception $e) {
            Log::error('Email sending failed: '.$e->getMessage(), [
                'attempt' => $attempt->id,
            ]);
        }
    }

    private function processSingleChoiceQuestion(Request $request, Attempt $attempt, array $question): void
    {
        $answerIds = Arr::pluck($question['answers'], 'id');

        $input = $request->validate([
            'answer' => ['required', Rule::in($answerIds)],
        ], [
            'answer.required' => __('messages.select_at_least_one_answer'),
            'answer.in' => __('messages.invalid_answer_selected'),
        ]);

        $isCorrect = Arr::get($question, 'answers.'.($input['answer'] - 1).'.state') == 1;

        $this->createOrUpdateResult($attempt, $question, $input['answer'], $isCorrect);
    }

    private function processMultiChoiceQuestion(Request $request, Attempt $attempt, array $question): ?string
    {
        $selectedAnswers = $request->input('a'.$question['id'], []);
        $validAnswerIds = Arr::pluck($question['answers'], 'id');
        $invalidAnswers = array_diff($selectedAnswers, $validAnswerIds);
        if (! empty($invalidAnswers)) {
            return __('messages.invalid_answer_selected');
        }
        $input = $request->validate([
            'a'.$question['id'] => 'required|array|min:1',
        ], [
            'a'.$question['id'].'.required' => __('messages.select_at_least_one_answer'),
        ]);

        $correctAnswers = [];
        foreach ($question['answers'] as $answer) {
            if ($answer['state']) {
                $correctAnswers[] = $answer['id'];
            }
        }
        sort($selectedAnswers);
        sort($correctAnswers);
        $isCorrect = $selectedAnswers == $correctAnswers;

        $this->createOrUpdateResult($attempt, $question, $selectedAnswers, $isCorrect);

        return null;
    }

    private function processOrderQuestion(Request $request, Attempt $attempt, array $question): ?string
    {
        $selectedOrder = array_filter(Arr::wrap($request->input($question['id'], [])));
        if (empty($selectedOrder)) {
            return __('messages.select_at_least_one_answer');
        }

        $correctOrder = [];
        foreach ($question['answers'] as $answer) {
            if (isset($answer['order'])) {
                $correctOrder[$answer['id']] = $answer['order'];
            }
        }
        $validAnswerIds = array_column($question['answers'], 'id');
        foreach ($selectedOrder as $answerId => $selectedPosition) {
            if (! in_array($answerId, $validAnswerIds)) {
                return __('messages.invalid_answer_selected');
            }
        }

        $uniqueOrderNumbers = array_unique($selectedOrder);
        if (count($selectedOrder) !== count($uniqueOrderNumbers)) {
            return __('messages.duplicate_order_numbers');
        }

        $isCorrect = true;
        foreach ($selectedOrder as $answerId => $selectedPosition) {
            if (isset($correctOrder[$answerId]) && $selectedPosition != $correctOrder[$answerId]) {
                $isCorrect = false;
                break;
            }
        }

        $this->createOrUpdateResult($attempt, $question, $selectedOrder, $isCorrect);

        return null;
    }

    private function processImageQuestion(Request $request, Attempt $attempt, array $question): ?string
    {
        $validationRules = [];
        $customAnswers = [];

        foreach ($question['answers'] as $answer) {
            $answerId = $answer['id'];
            $validationRules["answer_$answerId"] = 'required|string';
            $customAnswers[$answer['id']] = $request->input("answer_$answerId");
        }
        $request->validate($validationRules, [
            'required' => __('messages.image_custom_field_required'),
        ]);

        if (in_array('', $customAnswers)) {
            return __('messages.image_custom_field_empty');
        }

        $this->createOrUpdateResult($attempt, $question, $customAnswers, false);

        return null;
    }

    protected function createOrUpdateResult(Attempt $attempt, array $question, $answer, bool $isCorrect): void
    {
        $result = $attempt->result()->where('question_id', $question['id'])->first();
        if ($result) {
            $result->update([
                'answer' => $answer,
                'is_correct' => $isCorrect,
            ]);
        } else {
            Result::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question['id'],
                'question' => $question['text'],
                'answer' => $answer,
                'is_correct' => $isCorrect,
            ]);
        }
    }
}
