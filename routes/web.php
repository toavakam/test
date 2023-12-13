<?php

use App\Http\Controllers\AttemptController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::get('/{lang?}', [TestController::class, 'home'])->name('home');

Route::get('{lang}/test/{pk}', [TestController::class, 'index'])->name('dashboard');
Route::post('{lang}/test/{pk}', [TestController::class, 'greet']);

Route::get('{lang}/question/{pk}/{num?}', [AttemptController::class, 'question'])->name('question');
Route::post('{lang}/question/{pk}/{num?}', [AttemptController::class, 'PostAnswers'])->name('answer');

Route::get('{lang}/finish/{pk}', [AttemptController::class, 'finish'])->name('finish');
