<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class TriviaController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function startGame()
    {
        return $this->index(request());
    }

    public function index(Request $request)
    {
        $request->session()->forget([
            'trivia_queue', 'current_index', 'score', 'fails', 'answer_history'
        ]);
        $triviaSet = $this->preloadTrivia();
        $gameId = Str::random(20);
        $request->session()->put("games.$gameId", [
            'trivia_queue' => $triviaSet,
            'current_index' => 0,
            'score' => 0,
            'fails' => 0,
            'answer_history' => [],
        ]);

        return redirect()->route('trivia', ['game' => $gameId]);
    }

    public function getQuestion(Request $request, $game)
    {
        $gameKey = "games.$game";
        $gameData = $request->session()->get($gameKey);

        if (!$gameData) {
            return redirect()->route('start');
        }

        $queue = $gameData['trivia_queue'];
        $index = $gameData['current_index'];
        $score = $gameData['score'];
        $fails = $gameData['fails'];
        $history = $gameData['answer_history'];

        if (empty($queue)) {
            return redirect()->route('start');
        }

        if ($fails >= 3 || $index >= 20 || $index >= count($queue)) {
            return redirect()->route('result', ['game' => $game]);
        }

        $questionData = $queue[$index];
        $question = $questionData['question'];
        $options = $questionData['options'];
        $answer = $questionData['answer'];

        if ($request->isMethod('post') && $request->has('selected')) {
            $selected = $request->input('selected');
            $isCorrect = $selected == $answer;

            $score += $isCorrect ? 1 : 0;
            $fails += $isCorrect ? 0 : 1;

            $history[] = compact('question', 'selected') + [
                'correct' => $answer,
                'is_correct' => $isCorrect,
            ];

            $request->session()->put($gameKey, [
                'trivia_queue' => $queue,
                'current_index' => $index + 1,
                'score' => $score,
                'fails' => $fails,
                'answer_history' => $history,
            ]);

            $request->session()->flash('feedback', [
                'is_correct' => $isCorrect,
                'question' => $question,
                'selected' => $selected,
                'correct' => $answer,
            ]);

            return redirect()->route('trivia', ['game' => $game]);
        }

        return view('trivia', compact('question', 'options', 'game'));
    }

    public function showResult(Request $request, $game)
    {
        $gameKey = "games.$game";
        $gameData = $request->session()->get($gameKey);

        if (!$gameData) {
            return redirect()->route('start');
        }

        $score = $gameData['score'];
        $fails = $gameData['fails'];
        $history = $gameData['answer_history'];

        $request->session()->forget($gameKey);

        return view('result', compact('score', 'fails', 'history'));
    }

    private function preloadTrivia(): array
    {
        $desiredCount = 20;
        $triviaSet = [];

        $numbers = collect(range(1, 300))->shuffle()->take(50);

        $numberList = $numbers->implode(',');

        try {
            $response = Http::timeout(3)->get("http://numbersapi.com/{$numberList}/trivia?fragment&notfound=floor");

            if ($response->ok()) {
                $data = $response->json();

                foreach ($numbers as $number) {
                    if (isset($data[$number]) && count($triviaSet) < $desiredCount) {
                        $fact = $data[$number];

                        $options = $this->generateOptions($number);

                        $triviaSet[] = [
                            'question' => $fact,
                            'options' => $options,
                            'answer' => $number,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
                Log::error("API failed: " . $e->getMessage());
                return [];
        }

        return $triviaSet;
    }

    private function generateOptions(int $number): array
    {
        $options = collect([
            $number,
            $number + rand(1, 5),
            $number - rand(1, 5),
            $number + rand(6, 10),
            $number - rand(6, 10),
        ])->filter(fn($n) => $n > 0)->unique()->all();

        return Arr::random($options, min(4, count($options)));
    }
}
