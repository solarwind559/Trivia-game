<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TriviaController;

Route::get('/', [TriviaController::class, 'welcome'])->name('welcome');
Route::get('/start', [TriviaController::class, 'startGame'])->name('start');
Route::match(['get', 'post'], '/trivia', [TriviaController::class, 'getQuestion'])->name('trivia');
Route::get('/result', [TriviaController::class, 'showResult'])->name('result');
