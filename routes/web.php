<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TriviaController;

Route::get('/', [TriviaController::class, 'index']);
Route::get('/trivia', [TriviaController::class, 'getQuestion'])->name('trivia');
