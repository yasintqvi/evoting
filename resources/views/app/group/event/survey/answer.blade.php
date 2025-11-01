@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">
                پاسخ نظرسنجی
            </h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">نظرسنجی</a></li>
                <li class="breadcrumb-item active">پاسخ به نظرسنجی</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="mb-3">{{ $survey->title }}</h4>
            @if ($survey->description)
                <p class="text-muted">{{ $survey->description }}</p>
            @endif

            <form action="{{ route('surveys.answer.store', [$group->slug, $event->slug, $survey->slug]) }}" method="POST">
                @csrf

                @foreach ($survey->questions as $question)
                    <div class="mb-3 border p-3 rounded">
                        <label class="form-label d-block mb-2">
                            {{ $question->question_text }}
                            @if ($question->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        <div class="d-flex flex-wrap gap-3"> {{-- گزینه‌ها افقی --}}
                            @if ($question->type == 1)
                                {{-- تک انتخابی --}}
                                @foreach ($question->options as $option)
                                    <div class="form-check form-check-inline">
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
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox"
                                            name="questions_{{ $question->id }}[]" value="{{ $option->id }}"
                                            id="option_{{ $option->id }}">
                                        <label class="form-check-label" for="option_{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                        <div class="mt-2">
                            @error('questions_' . $question->id)
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">ارسال پاسخ‌ها</button>
            </form>
        </div>
    </div>
@endsection
