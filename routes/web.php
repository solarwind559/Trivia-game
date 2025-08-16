<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TriviaController;

Route::get('/', [TriviaController::class, 'index']);
Route::get('/question', [TriviaController::class, 'getQuestion']);
