<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class TriviaController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    public function startGame()
    {
        $this->preloadTrivia();
        return $this->index(request());
    }

    public function index(Request $request)
    {
        $request->session()->flush();
        $triviaSet = $this->preloadTrivia();
        $request->session()->put([
            'trivia_queue' => $triviaSet,
            'current_index' => 0,
            'score' => 0,
            'fails' => 0,
            'answer_history' => [],
        ]);

        return redirect()->route('trivia');
    }

    public function getQuestion(Request $request)
    {
        $queue = $request->session()->get('trivia_queue', []);
        $index = $request->session()->get('current_index', 0);
        $score = $request->session()->get('score', 0);
        $fails = $request->session()->get('fails', 0);

        if (empty($queue)) {
            return redirect()->route('start');
        }

        if ($fails >= 3 || $index >= 20 || $index >= count($queue)) {
            return redirect()->route('result');
        }

        $questionData = $queue[$index];
        $question = $questionData['question'];
        $options = $questionData['options'];
        $answer = $questionData['answer'];

        if ($request->isMethod('post') && $request->has('selected')) {
            $selected = $request->input('selected');

            if ($selected == $answer) {
                $request->session()->put('score', $score + 1);
            } else {
                $request->session()->put('fails', $fails + 1);
            }

            $history = $request->session()->get('answer_history', []);
            $history[] = compact('question', 'selected') + [
                'correct' => $answer,
                'is_correct' => $selected == $answer,
            ];

            $request->session()->put([
                'answer_history' => $history,
                'current_index' => $index + 1,
            ]);

            $request->session()->flash('feedback', [
                'is_correct' => $selected == $answer,
                'question' => $question,
                'selected' => $selected,
                'correct' => $answer,
            ]);

            return redirect()->route('trivia');
        }

        return view('trivia', compact('question', 'options'));
    }

    public function showResult(Request $request)
    {
        $score = $request->session()->get('score', 0);
        $fails = $request->session()->get('fails', 0);
        $history = $request->session()->get('answer_history', []);

        $request->session()->forget([
            'score', 'fails', 'current_index', 'trivia_queue', 'answer_history'
        ]);

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
        } catch (\Exception $e) {}

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
