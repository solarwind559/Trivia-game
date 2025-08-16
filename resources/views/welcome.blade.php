@extends('layouts.master')

@section('title', 'Welcome')

@section('content')

<h1 class="mt-5 mb-3 text-center">Welcome to the Trivia Game</h1>

<div class="text-center">
    <a href="{{ route('start') }}" class="btn btn-primary">
        Start
    </a>
</div>

@endsection
