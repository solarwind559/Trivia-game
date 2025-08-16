@extends('layouts.master')

@section('title', 'Trivia page')

@section('content')

<h1 class="my-5 text-center">Welcome to the Trivia Game</h1>

<div class="text-center">
    <a href="{{ route('trivia') }}" class="btn btn-primary">
        Start
    </a>
</div>

@endsection
