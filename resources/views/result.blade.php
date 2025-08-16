@extends('layouts.master')

@section('title', 'Results')

@section('content')

    <h1 class="mt-5 mb-3 text-center">Results</h1>

    <div class="text-center my-4">

        <h3>Correct answers: {{ $score }} out of {{ count($history) }} questions.</h3>
        <h4>Incorrect answers: {{ $fails }}</h4>

        @php
            $lastWrong = collect($history)->last(fn($entry) => !$entry['is_correct']);
        @endphp

        @if ($lastWrong)
            <div class="mt-4 alert alert-warning text-left">
                <strong>Last incorrect question:</strong><br>
                What is {{ $lastWrong['question'] }}?<br>
                <strong>Your answer:</strong> {{ $lastWrong['selected'] }}<br>
                <strong>Correct answer:</strong> {{ $lastWrong['correct'] }}
            </div>
        @endif

        <a href="{{ route('start') }}" class="btn btn-primary mt-4">Play Again</a>
    </div>

@endsection
