<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class AttemptController extends Controller
{
    public function question($lang, int $pk, int $num = 1)
    {
        $attempt = Attempt::findOrFail($pk);
        $test = $attempt->test;
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';

        App::setLocale($lang);

        $questions = $test->getQuestions($lang);

        if (! ($question = Arr::get($questions, $num - 1))) {
            return redirect()->route('finish', ['pk' => $pk, 'lang' => $lang]);
        }

        $result = Result::where('attempt_id', $pk)
            ->where('question', $question['text'])
            ->first();

        $userAnswer = old('a'.$question['id']) ?: ($result ? $result->answer : null);

        $bar = count($questions);
        $percentage = ($num / $bar) * 100;

        return view('questions', compact('question', 'pk', 'num', 'lang', 'test', 'userAnswer', 'bar', 'percentage'));
    }

    public function PostAnswers(Request $request, $lang, int $pk, int $num = 1)
    {

        $attempt = Attempt::findOrFail($pk);
        $test = $attempt->test;
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';

        App::setLocale($lang);
        $questions = $test->lv['questions'];
        if (! ($question = Arr::get($questions, $num - 1))) {
            return redirect()->route('finish', ['pk' => $pk]);
        }
        $question = $questions[$num - 1];

        if ($question['type'] === 'single-choice') {
            $request->validate([
                'answer' => 'required|in:'.implode(',', array_keys($question['answers'])),
            ], [
                'answer.required' => __('messages.select_at_least_one_answer'),
                'answer.in' => __('messages.invalid_answer_selected'),
            ]);

        } elseif ($question['type'] === 'multiple-choice') {
            $selectedAnswers = $request->input('a'.$question['id'], []);
            $validAnswers = array_keys($question['answers']);

            foreach ($selectedAnswers as $selectedAnswer) {
                if (! in_array($selectedAnswer, $validAnswers)) {
                    return redirect()->back()->withErrors([
                        $question['id'] => __('messages.invalid_answer_selected'),
                    ]);
                }
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
        }

        if ($question['type'] === 'single-choice') {
            $result = Result::where('attempt_id', $pk)
                ->where('question', $question['text'])
                ->first();

            if ($result) {
                $result->update([
                    'answer' => $request->input('answer'),
                    'is_correct' => $question['answers'][$request->input('answer') - 1]['state'] == 1,
                ]);
            } else {
                Result::create([
                    'attempt_id' => $pk,
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
                ->first();

            if ($result) {
                $result->update([
                    'answer' => ($selectedAnswers),
                    'is_correct' => $isCorrect,
                ]);
            } else {
                Result::create([
                    'attempt_id' => $pk,
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
                    ->first();

                if ($result) {
                    $result->update([
                        'answer' => empty($selectedOrder) ? null : ($selectedOrder),
                        'is_correct' => $isCorrect,
                    ]);
                } else {
                    Result::create([
                        'attempt_id' => $pk,
                        'question' => $question['text'],
                        'answer' => empty($selectedOrder) ? null : ($selectedOrder),
                        'is_correct' => $isCorrect,
                    ]);
                }
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

        return view('result', compact('pk', 'lang', 'test', 'percentage'));
    }
}
