<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TestController extends Controller
{

    public function home(Request $request, $lang = 'en')
    {
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
        App::setLocale($lang);
        
        if ($request->isMethod('post')) {
            $testId = $request->input('test');
            return redirect()->route('dashboard', ['lang' => $lang, 'pk' => $testId]);
        }
        
        $tests = Test::all();

        return view('home', compact('tests'));
    }
    public function index($lang, int $pk)
    {
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
        App::setLocale($lang);
        $test = Test::findOrFail($pk);

        return view('main', compact('test', 'lang', 'pk'));
    }

    public function greet(Request $request, $lang, int $pk)
    {
        $test = Test::findOrFail($pk);
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
        App::setLocale($lang);

        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
        ]);
        $questions = $test->getQuestions($lang);
        shuffle($questions);

        $attempt = Attempt::create([
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'completed' => false,
            'question_count' => count($test->getQuestions($lang)),
            'correct_answer_count' => 0,
            'test_id' => $test->id,
            'QuestionOrder' => $questions,
        ]);

        return to_route('question', ['pk' => $attempt->id, 'num' => 1, 'lang' => $lang]);

    }

    public function question(int $pk, int $num = 0)
    {
        $attempt = Attempt::findOrFail($pk);
        $shuffledQuestions = $attempt->QuestionOrder;

        if ($num > 0 && $num <= count($shuffledQuestions)) {
            $currentQuestion = $shuffledQuestions[$num - 1];

            return view('questions', compact('currentQuestion', 'num'));
        }
    }
}
