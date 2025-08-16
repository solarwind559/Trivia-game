@extends('layouts.master')

@section('title', 'Trivia page')

@section('content')
    <h1 class="my-5">Questions</h1>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @else
        <div class="my-4">
            <h2 class="mx-auto">{{ $question }}</h2>
        </div>

        <form method="POST" action="{{ route('trivia') }}">
            @csrf
            <div class="row">
                @foreach ($options as $option)
                    <div class="col-6 mb-3">
                        <button type="submit" name="selected" value="{{ $option }}"
                            class="btn btn-outline-primary w-100">
                            {{ $option }}
                        </button>
                    </div>
                @endforeach
            </div>
        </form>
    @endif

@endsection
