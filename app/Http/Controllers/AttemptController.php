<?php

namespace App\Http\Controllers;

use App\Mail\TestResult;
use App\Models\Attempt;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AttemptController extends Controller
{
    public function question($lang, int $pk, int $num = 1)
    {

        $attempt = Attempt::findOrFail($pk);
        $test = $attempt->test;
        $lang = array_key_exists($lang, config('app.languages')) ? $lang : 'lv';

        App::setLocale($lang);

        $shuffledQuestions = $attempt->QuestionOrder;

        if (! ($question = Arr::get($shuffledQuestions, $num - 1))) {
            $this->sendTestResultEmail($attempt, $lang);
            return to_route('finish', ['pk' => $pk, 'lang' => $lang]);
        }
        if (in_array($question['type'], ['single-choice', 'multiple-choice', 'order'])) {
            shuffle($question['answers']);
        }

        $result = $attempt->result()->where('question_id', $question['id'])->first();
        $userAnswer = $result ? $result->answer : null;
        $bar = count($shuffledQuestions);
        $percentage = ($num / $bar) * 100;

        return view('questions', compact('question', 'pk', 'num', 'lang', 'test', 'userAnswer', 'bar', 'percentage', 'attempt'));
    }

    public function PostAnswers(Request $request, $lang, int $pk, int $num = 1)
    {

        $attempt = Attempt::findOrFail($pk);
        $test = $attempt->test;
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';

        App::setLocale($lang);
        $questions = $attempt->QuestionOrder;

        if (! ($question = Arr::get($questions, $num - 1))) {


            return redirect()->route('finish', ['pk' => $pk]);
        }
        $question = $questions[$num - 1];

        if ($question['type'] === 'single-choice') {
            $answerIds = Arr::pluck($question['answers'], 'id');

            $request->validate([
                'answer' => [
                    'required',
                    Rule::in($answerIds),
                ],
            ], [
                'answer.required' => __('messages.select_at_least_one_answer'),
                'answer.in' => __('messages.invalid_answer_selected'),
            ]);
        } elseif ($question['type'] === 'multiple-choice') {
            $selectedAnswers = $request->input('a'.$question['id'], []);
            $validAnswerIds = Arr::pluck($question['answers'], 'id');
            $invalidAnswers = array_diff($selectedAnswers, $validAnswerIds);
            if (! empty($invalidAnswers)) {
                return redirect()->back()->withErrors([
                    $question['id'] => __('messages.invalid_answer_selected'),
                ]);
            }
            $request->validate([
                'a'.$question['id'] => 'required|array|min:1',
            ], [
                'a'.$question['id'].'.required' => __('messages.select_at_least_one_answer'),
            ]);

        } elseif ($question['type'] === 'order') {
            $selectedOrder = $request->input($question['id'], []);
            $correctOrder = [];

            foreach ($question['answers'] as $answer) {
                if (isset($answer['order'])) {
                    $correctOrder[$answer['id']] = $answer['order'];
                }
            }
            $validAnswerIds = array_column($question['answers'], 'id');
            foreach ($selectedOrder as $answerId => $selectedPosition) {
                if (! in_array($answerId, $validAnswerIds)) {
                    return redirect()->back()->withErrors([
                        $question['id'] => __('messages.invalid_answer_selected'),
                    ]);
                }
            }

            $isCorrect = true;

            $selectedOrder = array_filter($selectedOrder);

            if (empty($selectedOrder)) {
                return redirect()->back()->withErrors([
                    $question['id'] => __('messages.select_at_least_one_answer'),
                ]);
            }
            $uniqueOrderNumbers = array_unique($selectedOrder);

            if (count($selectedOrder) !== count($uniqueOrderNumbers)) {
                return redirect()->back()->withErrors([
                    $question['id'] => __('messages.duplicate_order_numbers'),
                ]);
            }
            foreach ($selectedOrder as $answerId => $selectedPosition) {
                if (isset($correctOrder[$answerId]) && $selectedPosition != $correctOrder[$answerId]) {
                    $isCorrect = false;
                    break;
                }
            }
        }if ($question['type'] === 'image-custom') {
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
                return redirect()->back()->withErrors([
                    $question['id'] => __('messages.image_custom_field_empty'),
                ]);
            }
        }

        if ($question['type'] === 'single-choice') {
            $result = Result::where('attempt_id', $pk)
                ->where('question', $question['text'])
                ->where('question_id', $question['id'])
                ->first();

            if ($result) {
                $result->update([
                    'answer' => $request->input('answer'),
                    'is_correct' => $question['answers'][$request->input('answer') - 1]['state'] == 1,
                ]);
            } else {
                Result::create([
                    'attempt_id' => $pk,
                    'question_id' => $question['id'],
                    'question' => $question['text'],
                    'answer' => $request->input('answer'),
                    'is_correct' => $question['answers'][$request->input('answer') - 1]['state'] == 1,
                ]);
            }
        } elseif ($question['type'] === 'multiple-choice') {
            $selectedAnswers = $request->input('a'.$question['id'], []);
            $correctAnswers = [];

            foreach ($question['answers'] as $answer) {
                if ($answer['state']) {
                    $correctAnswers[] = $answer['id'];
                }
            }
            sort($selectedAnswers);
            sort($correctAnswers);
            $isCorrect = $selectedAnswers == $correctAnswers;

            $result = Result::where('attempt_id', $pk)
                ->where('question', $question['text'])
                ->where('question_id', $question['id'])
                ->first();

            if ($result) {
                $result->update([
                    'answer' => ($selectedAnswers),
                    'is_correct' => $isCorrect,
                ]);
            } else {
                Result::create([
                    'attempt_id' => $pk,
                    'question_id' => $question['id'],
                    'question' => $question['text'],
                    'answer' => ($selectedAnswers),
                    'is_correct' => $isCorrect,
                ]);
            }
        } elseif ($question['type'] === 'order') {
            if ($request->has($question['id'])) {
                $selectedOrder = $request->input($question['id'], []);
                $correctOrder = [];
                $isCorrect = true;

                foreach ($question['answers'] as $answer) {
                    if (isset($answer['order'])) {
                        $correctOrder[$answer['id']] = $answer['order'];
                    }
                }

                if (empty($selectedOrder)) {
                } else {
                    foreach ($selectedOrder as $answerId => $selectedPosition) {
                        if (isset($correctOrder[$answerId]) && $selectedPosition != $correctOrder[$answerId]) {
                            $isCorrect = false;
                            break;
                        }
                    }
                }
                $result = Result::where('attempt_id', $pk)
                    ->where('question', $question['text'])
                    ->where('question_id', $question['id'])
                    ->first();

                if ($result) {
                    $result->update([
                        'answer' => empty($selectedOrder) ? null : ($selectedOrder),
                        'is_correct' => $isCorrect,
                    ]);
                } else {
                    Result::create([
                        'attempt_id' => $pk,
                        'question_id' => $question['id'],
                        'question' => $question['text'],
                        'answer' => empty($selectedOrder) ? null : ($selectedOrder),
                        'is_correct' => $isCorrect,
                    ]);
                }
            }
        }
        if ($question['type'] === 'image-custom') {
            $result = Result::where('attempt_id', $pk)
                ->where('question_id', $question['id'])
                ->first();

            $customAnswers = [];

            foreach ($question['answers'] as $answer) {
                $answerId = $answer['id'];
                $customAnswers[$answer['id']] = $request->input("answer_$answerId");
            }

            if ($result) {
                $result->update([
                    'answer' => ($customAnswers),
                    'is_correct' => false,
                ]);
            } else {
                Result::create([
                    'attempt_id' => $pk,
                    'question_id' => $question['id'],
                    'question' => $question['text'],
                    'answer' => ($customAnswers),
                    'is_correct' => false,
                ]);
            }
        }
        $correctAnswerCount = $attempt->result()->where('is_correct', true)->count();

        $attempt->update(['correct_answer_count' => $correctAnswerCount]);

        $nextNum = $num + 1;

        return redirect()->route('question', ['pk' => $pk, 'num' => $nextNum, 'lang' => $lang]);
    }

    public function finish($lang, int $pk)
    {
            $attempt = Attempt::findOrFail($pk);

            $test = $attempt->test;
            $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
            App::setLocale($lang);
            $questions = $test->getQuestions($lang);


            $totalQuestions = count($questions);
            $correctAnswerCount = $attempt->correct_answer_count;
            $percentage = round(($correctAnswerCount / $totalQuestions) * 100);
            $hasImageCustomQuestion = $test->hasImageCustomQuestion();



        return view('result', ['hasImageCustomQuestion' => $hasImageCustomQuestion], compact('pk', 'lang', 'test', 'percentage'));
    }
    private function sendTestResultEmail(Attempt $attempt, string $lang)
    {
        try {
            $testemail = config('app.report_email');
            Mail::to($testemail)->send(new TestResult($attempt, $lang));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
    }
}
