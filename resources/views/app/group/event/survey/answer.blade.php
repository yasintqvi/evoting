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

            {{-- نمایش زمان شروع --}}
            @if ($survey->start_at)
                <div class="alert alert-secondary d-flex align-items-center gap-2 mb-3">
                    <i class="ti ti-calendar-event fs-18"></i>
                    <div>
                        <strong>زمان شروع نظرسنجی:</strong>
                        {{ verta($survey->start_at)->format('Y/m/d H:i') }}
                    </div>
                </div>
            @endif

            {{-- نمایش زمان پایان --}}
            @if ($survey->end_at)
                <div class="alert {{ $isExpired ? 'alert-danger' : 'alert-info' }} d-flex align-items-center gap-2 mb-4">
                    <i class="ti ti-clock fs-18"></i>
                    <div>
                        @if ($isExpired)
                            <strong>این نظرسنجی منقضی شده است.</strong>
                            زمان پایان: {{ verta($survey->end_at)->format('Y/m/d H:i') }}
                        @else
                            <strong>زمان پایان نظرسنجی:</strong>
                            {{ verta($survey->end_at)->format('Y/m/d H:i') }}
                            <div>
                                <small class="text-primary survey-countdown" data-end="{{ $survey->end_at?->toDateTimeString() }}">
                                    زمان باقی‌مانده: <span class="value"></span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if ($isExpired)
                <div class="text-center py-5">
                    <i class="ti ti-lock fs-48 text-danger"></i>
                    <p class="text-muted mt-3">زمان پاسخ‌دهی به این نظرسنجی به اتمام رسیده است.</p>
                </div>
            @elseif ($isNotStarted)
                <div class="text-center py-5">
                    <i class="ti ti-clock-pause fs-48 text-warning"></i>
                    <p class="text-muted mt-3">این نظرسنجی هنوز شروع نشده است.</p>
                </div>
            @elseif ($hasSubmitted)
                <div class="text-center py-5">
                    <i class="ti ti-circle-check fs-48 text-success"></i>
                    <p class="text-muted mt-3 mb-0">پاسخ شما برای این نظرسنجی ثبت شده است. هر سهام‌دار فقط یک بار می‌تواند شرکت کند.</p>
                </div>
            @else
                <form action="{{ route('surveys.answer.store', [$group->slug, $event->slug, $survey->slug]) }}" method="POST">
                    @csrf

                    @error('survey')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    @if ($group->type === App\Enums\GroupType::SPECIAL)
                        @php
                            $participant = $event->participants()->where('user_id', user()->id)->whereNull('attorney_id')->first();
                            $normal = $participant?->normal_stock_count ?? 0;
                            $prefered = $participant?->prefered_stock_count ?? 0;
                            $effective = $normal + $prefered;
                        @endphp
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                            <i class="ti ti-chart-bar fs-18"></i>
                            <div>
                                <strong>سهام شما در این رویداد (سهامی خاص):</strong>
                                عادی: {{ $normal }} | ممتاز: {{ $prefered }} | کل: {{ $effective }}
                            </div>
                        </div>
                    @endif

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
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            function formatDuration(ms) {
                if (ms <= 0) return '0:00:00';
                var totalSeconds = Math.floor(ms / 1000);
                var days = Math.floor(totalSeconds / 86400);
                totalSeconds %= 86400;
                var hours = Math.floor(totalSeconds / 3600);
                var minutes = Math.floor((totalSeconds % 3600) / 60);
                var seconds = totalSeconds % 60;
                if (days > 0) {
                    return days + ' روز ' + String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                }
                return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            }
            function tick() {
                var items = document.querySelectorAll('.survey-countdown');
                var now = new Date().getTime();
                items.forEach(function (el) {
                    var endAttr = el.getAttribute('data-end');
                    if (!endAttr) return;
                    var endTime = new Date(endAttr.replace(' ', 'T')).getTime();
                    var remaining = endTime - now;
                    var valueEl = el.querySelector('.value');
                    if (valueEl) {
                        valueEl.textContent = formatDuration(remaining);
                    }
                    if (remaining <= 0) {
                        el.classList.remove('text-primary');
                        el.classList.add('text-danger');
                    }
                });
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
