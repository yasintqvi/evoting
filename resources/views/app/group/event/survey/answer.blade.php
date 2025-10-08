@extends('app.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $survey->title }}</h4>
            @if ($survey->description)
                <p class="text-muted">{{ $survey->description }}</p>
            @endif

            <form action="{{ route('surveys.answer.store', [$group->slug, $event->id, $survey->id]) }}" method="POST">
                @csrf

                @foreach ($survey->questions as $question)
                    <div class="mb-3 border p-2 rounded">
                        <label class="form-label d-block">{{ $question->question_text }}
                            @if ($question->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if ($question->type == 1)
                            {{-- تک انتخابی --}}
                            @foreach ($question->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="questions_{{ $question->id }}"
                                        value="{{ $option->id }}" id="option_{{ $option->id }}">
                                    <label class="form-check-label" for="option_{{ $option->id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach
                        @elseif ($question->type == 2)
                            {{-- چند انتخابی --}}
                            @foreach ($question->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="questions_{{ $question->id }}[]"
                                        value="{{ $option->id }}" id="option_{{ $option->id }}">
                                    <label class="form-check-label" for="option_{{ $option->id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">ارسال پاسخ‌ها</button>
            </form>
        </div>
    </div>
@endsection
