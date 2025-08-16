<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TriviaController extends Controller
{
    public function index()
    {
        return view('trivia');
    }

    public function getQuestion(Request $request)
    {
        $used = $request->session()->get('used_numbers', []);
        $number = $this->getUniqueRandomNumber($used);
        $response = Http::get("http://numbersapi.com/{$number}/trivia?json");

        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Failing to fetch trivia'], 500);
        }

        $request->session()->put('used_numbers', $used);
    }

    private function getUniqueRandomNumber(array $used)
    {
        do {
            $number = rand(1, 300);
        } while (in_array($number, $used));

        return $number;
    }
}
