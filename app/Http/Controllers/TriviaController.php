<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TriviaController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getQuestion(Request $request)
    {
        $used = $request->session()->get('used_numbers', []);
        $number = $this->getUniqueRandomNumber($used);

        $response = Http::get("http://numbersapi.com/{$number}/trivia?json");

        if (!$response->successful() || !$response['found']) {
            return view('trivia')->withErrors(['error' => 'Failed to fetch trivia.']);
        }

        $fact = $response['text'];
        $correctAnswer = $response['number'];

        $options = collect([$correctAnswer]);
        while ($options->count() < 4) {
            $fake = rand(1, 300);
            if (!$options->contains($fake)) {
                $options->push($fake);
            }
        }

        $shuffled = $options->shuffle();

        $used[] = $number;
        $request->session()->put('used_numbers', $used);

        return view('trivia', [
            'question' => $fact,
            'options' => $shuffled->values(),
            'answer' => $correctAnswer
        ]);
    }

    private function getUniqueRandomNumber(array $used)
    {
        do {
            $number = rand(1, 300);
        } while (in_array($number, $used));

        return $number;
    }
}
