<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{lang}/test/{pk}', [App\Http\Controllers\TestController::class, 'index'])->name('dashboard');
Route::post('{lang}/test/{pk}', [App\Http\Controllers\TestController::class, 'greet']);
Route::get('{lang}/question/{pk}/{num?}', [App\Http\Controllers\AttemptController::class, 'question'])->name('question');
Route::post('{lang}/question/{pk}/{num?}', [App\Http\Controllers\AttemptController::class, 'PostAnswers'])->name('answer');
Route::get('{lang}/finish/{pk}', [App\Http\Controllers\AttemptController::class, 'finish'])->name('finish');
