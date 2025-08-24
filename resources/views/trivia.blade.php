@extends('layouts.master')

@section('title', 'Trivia page')

@section('content')
    <h1 class="mt-5 mb-3">Questions</h1>

    @if (session('feedback'))
        @php $fb = session('feedback'); @endphp
        <div class="alert {{ $fb['is_correct'] ? 'alert-success' : 'alert-danger' }}">
            <strong>{{ $fb['is_correct'] ? 'Correct!' : 'Incorrect!' }}</strong><br>
            <strong>Question:</strong> What is {{ $fb['question'] }}?<br>
            <strong>Your answer:</strong> {{ $fb['selected'] }}<br>
            @if (!$fb['is_correct'])
                <strong>Correct answer:</strong> {{ $fb['correct'] }}
            @endif
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @elseif (!$question || empty($options))
        <div class="alert alert-danger">
            Oops! Something went wrong. Please try again later.
        </div>
    @else
        <div class="my-4">
            <h2>What is {{ $question }}?</h2>
        </div>

        <form method="POST" action="{{ route('trivia', ['game' => $game]) }}">
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
