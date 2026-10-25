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
                        {{ verta($survey->start_at->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') }}
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
                            زمان پایان: {{ verta($survey->end_at->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                        @else
                            <strong>زمان پایان نظرسنجی:</strong>
                            {{ verta($survey->end_at->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') }}
                            <div>
                                <small class="text-primary survey-countdown" data-end="{{ $survey->end_at?->toDateTimeString() }}">
                                    زمان باقی‌مانده: <span class="value"></span>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- کارت اطلاعات نظرسنجی: وکالت‌ها، ناشناس بودن، تأثیر سهام --}}
            <div class="card border mb-3">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-user-check fs-18 {{ $survey->is_anonymous ? 'text-success' : 'text-warning' }}"></i>
                                <div>
                                    <div class="small text-muted mb-0">ناشناس بودن پاسخ‌ها</div>
                                    <div class="fw-semibold">{{ $survey->is_anonymous ? 'ناشناس است' : 'ناشناس نیست' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ti ti-chart-bar fs-18 {{ $isWeighted ? 'text-primary' : 'text-muted' }}"></i>
                                <div>
                                    <div class="small text-muted mb-0">تأثیر تعداد سهام</div>
                                    <div class="fw-semibold">{{ $isWeighted ? 'مؤثر است' : 'مؤثر نیست (هر نفر یک رأی)' }}</div>
                                </div>
                            </div>
                        </div>
                        @if ($isWeighted)
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-chart-pie fs-18 text-info"></i>
                                    <div>
                                        <div class="small text-muted mb-0">سهام شخصی شما</div>
                                        <div class="fw-semibold">عادی: {{ number_format($ownStock['normal']) }} | ممتاز: {{ number_format($ownStock['prefered']) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-scale fs-18 text-dark"></i>
                                    <div>
                                        <div class="small text-muted mb-0">مجموع وزن رأی شما</div>
                                        <div class="fw-semibold">{{ number_format($effectiveStock, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($representedParticipants->isNotEmpty())
                        <hr class="my-3">
                        <div>
                            <div class="fw-semibold mb-2 d-flex align-items-center gap-1">
                                <i class="ti ti-shield-check text-warning"></i>
                                وکالت‌های شما در این رویداد
                                <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">{{ $representedParticipants->count() }} نفر</span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>موکل</th>
                                            @if ($isWeighted)
                                                <th class="text-center">سهم عادی</th>
                                                <th class="text-center">سهم ممتاز</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($delegatedStockPerPrincipal as $delegate)
                                            <tr>
                                                <td>{{ $delegate['name'] }}</td>
                                                @if ($isWeighted)
                                                    <td class="text-center">{{ number_format($delegate['normal']) }}</td>
                                                    <td class="text-center">{{ number_format($delegate['prefered']) }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                شما پاسخ این نظرسنجی را هم به نمایندگی از افراد بالا و هم برای خودتان، یک‌جا ثبت می‌کنید.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

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
                <form id="surveyAnswerForm" action="{{ route('surveys.answer.store', [$group->slug, $event->slug, $survey->slug]) }}" method="POST">
                    @csrf

                    @error('survey')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

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
                                                value="{{ $option->id }}" id="option_{{ $option->id }}"
                                                {{ (string) old('questions_' . $question->id) === (string) $option->id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="option_{{ $option->id }}">
                                                {{ $option->option_text }}
                                            </label>
                                        </div>
                                    @endforeach
                                @elseif ($question->type == 2)
                                    {{-- چند انتخابی --}}
                                    @php
                                        $oldMulti = old('questions_' . $question->id, []);
                                        $oldMulti = is_array($oldMulti) ? $oldMulti : [];
                                    @endphp
                                    @foreach ($question->options as $option)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox"
                                                name="questions_{{ $question->id }}[]" value="{{ $option->id }}"
                                                id="option_{{ $option->id }}"
                                                {{ in_array((string) $option->id, array_map('strval', $oldMulti), true) ? 'checked' : '' }}>
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

                    <button type="button" id="surveySubmitBtn" class="btn btn-primary">ارسال پاسخ‌ها</button>
                </form>

                {{-- مودال تایید ثبت پاسخ نظرسنجی --}}
                <div class="modal fade" id="surveyConfirmModal" tabindex="-1" aria-labelledby="surveyConfirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="surveyConfirmModalLabel">تایید ارسال پاسخ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">آیا از ثبت پاسخ‌های خود مطمئن هستید؟ پس از ارسال، امکان ویرایش پاسخ وجود ندارد.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                                <button type="button" id="surveyConfirmSubmitBtn" class="btn btn-success">بله، ارسال شود</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            var form = document.getElementById('surveyAnswerForm');
            var submitBtn = document.getElementById('surveySubmitBtn');
            var confirmModalEl = document.getElementById('surveyConfirmModal');
            var confirmSubmitBtn = document.getElementById('surveyConfirmSubmitBtn');
            if (form && submitBtn && confirmModalEl && confirmSubmitBtn) {
                var confirmModal = new bootstrap.Modal(confirmModalEl);
                submitBtn.addEventListener('click', function () {
                    if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
                        return;
                    }
                    confirmModal.show();
                });
                confirmSubmitBtn.addEventListener('click', function () {
                    confirmSubmitBtn.disabled = true;
                    confirmModal.hide();
                    form.submit();
                });
                form.addEventListener('submit', function () {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'در حال ارسال...';
                });
            }
        })();
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
