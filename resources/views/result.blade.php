@extends('layouts.master')

@section('title', 'Results')

@section('content')

    <h1 class="my-5 text-center">Results</h1>

    <div class="text-center my-4">

        <h3>Correct answers: {{ $score }} out of {{ count($history) }} questions.</h3>
        <h4>Incorrect answers: {{ $fails }}</h4>

        <a href="{{ route('start') }}" class="btn btn-primary mt-4">Play Again</a>

    </div>

@endsection
