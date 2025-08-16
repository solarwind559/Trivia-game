@extends('layouts.master')

@section('title', 'Trivia page')

@section('content')
    <h1 class="my-5">Questions</h1>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @else
        <div class="my-4">
            <h2 class="mx-auto">{{ $question }}</h4>
        </div>

        <form method="GET" action="/trivia">
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

        @if (request('selected'))
            <div class="mt-4">
                @if (request('selected') == $answer)
                    <div class="alert alert-success">Correct!</div>
                @else
                    <div class="alert alert-danger">
                        Incorrect! The correct answer was <strong>{{ $answer }}</strong>.
                    </div>
                @endif
            </div>
        @endif
    @endif
    <script>
        document.querySelector('a.btn-secondary')?.addEventListener('click', function (e) {
            e.preventDefault();
            fetch('/trivia')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#trivia-container');
                    document.querySelector('#trivia-container').replaceWith(newContent);
                });

        });
    </script>

@endsection
