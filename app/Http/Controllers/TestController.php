<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TestController extends Controller
{
    public function index($lang = null, int $pk)
    {
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
        App::setLocale($lang);
        $test = Test::findOrFail($pk);


        return view('main', compact('test', 'lang', 'pk'));
    }

    public function greet(Request $request, $lang = null, int $pk)
    {
        $test = Test::findOrFail($pk);
        $lang = in_array($lang, ['en', 'lv', 'ru']) ? $lang : 'lv';
        App::setLocale($lang);

        $request->validate([
            'name' => 'required',
            'lastname' => 'required',
        ]);

        $attempt = Attempt::create([
            'name' => $request->input('name'),
            'lastname' => $request->input('lastname'),
            'completed' => false,
            'question_count' => count($test->getQuestions($lang)),
            'correct_answer_count' => 0,
            'test_id' => $test->id,
        ]);

        return to_route("question", ['pk'=>$attempt->id, 'num'=>1, 'lang'=>$lang]);
    }

    public function question(int $pk, int $num = 0)
    {
        return view('questions');
    }
}
