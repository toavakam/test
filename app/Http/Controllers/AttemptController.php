<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Result;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class AttemptController extends Controller
{
    public function question($lang=null, int $pk, int $num = 1)
    {
        $attempt = Attempt::findOrFail($pk);
        $test = $attempt->test;
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';


        App::setLocale($lang);

        $questions = $test->getQuestions($lang);


        if (! ($question = Arr::get($questions, $num - 1))) {
            return redirect()->route('finish', ['pk' => $pk, 'lang'=>$lang]);
        }

        $result = Result::where('attempt_id', $pk)
            ->where('question', $question['text'])
            ->first();

        $userAnswer = $result ? $result->answer : null;

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

        if($question['type']==='single-choice') {
            $request->validate([
                'answer' => 'required'
            ]);

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
            $request->validate([
                $question['id'] . '.*' => 'required'
            ]);

            $selectedAnswers = $request->input($question['id'], []);
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

//        dd(Attempt::query()->where('id', $pk));
        $attempt->update(['correct_answer_count' => $correctAnswerCount]);



        $nextNum = $num + 1;
        return redirect()->route('question', ['pk' => $pk, 'num' => $nextNum, 'lang'=>$lang]);
    }
    public function finish($lang = null, int $pk)
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