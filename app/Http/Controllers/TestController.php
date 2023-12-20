<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class TestController extends Controller
{
    public function home(Request $request, $lang = 'lv')
    {
        $this->setCurrentLocale($lang);

        if ($request->isMethod('post')) {
            $testId = $request->input('test');

            return redirect()->route('dashboard', ['lang' => $lang, 'pk' => $testId]);
        }
        $tests = Test::all();

        return view('home', compact('tests'));
    }

    public function index(string $lang, int $pk)
    {
        $this->setCurrentLocale($lang);

        $test = Test::findOrFail($pk);

        return view('main', compact('test'));
    }

    public function greet(Request $request, string $lang, int $pk): RedirectResponse
    {
        $this->setCurrentLocale($lang);

        $test = Test::findOrFail($pk);

        $input = $request->validate([
            'name' => 'required|string|max:250',
            'lastname' => 'required|string|max:250',
        ]);

        $questions = Arr::shuffle($test->getQuestions(App::currentLocale()));

        $attempt = Attempt::create([
            'name' => Arr::get($input, 'name'),
            'lastname' => Arr::get($input, 'lastname'),
            'completed' => false,
            'question_count' => count($questions),
            'correct_answer_count' => 0,
            'test_id' => $test->id,
            'QuestionOrder' => $questions,
        ]);

        return to_route('question', ['pk' => $attempt->id, 'num' => 1, 'lang' => App::currentLocale()]);
    }
}
