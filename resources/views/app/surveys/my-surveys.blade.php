@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">نظرسنجی‌های من</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
                <li class="breadcrumb-item active">نظرسنجی‌های من</li>
            </ol>
        </div>
    </div>

    @if ($availableSurveys->count() > 0)
        <div class="row">
            @foreach ($availableSurveys as $item)
                @php
                    $survey  = $item['survey'];
                    $event   = $item['event'];
                    $group   = $item['group'];
                @endphp
                <div class="col-xl-4 col-lg-12">
                    <div class="card border border-dashed h-100">
                        <div class="card-header border-bottom border-dashed bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1 fw-semibold">{{ $survey->title }}</h5>
                                    <p class="text-muted mb-0 fs-13">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                        {{ $event->title }} - {{ $group->title }}
                                    </p>
                                </div>
                                <span class="badge badge-soft-info">فعال</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($survey->description)
                                <p class="text-muted fs-13 mb-3">{{ $survey->description }}</p>
                            @endif

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 border border-dashed rounded bg-light">
                                        <h6 class="mb-0 text-primary fw-bold">{{ $survey->questions->count() }}</h6>
                                        <small class="text-muted">تعداد سوال</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 border border-dashed rounded bg-light">
                                        <h6 class="mb-0 text-secondary fw-bold">
                                            {{ $survey->is_anonymous ? 'بله' : 'خیر' }}
                                        </h6>
                                        <small class="text-muted">ناشناس</small>
                                    </div>
                                </div>
                            </div>

                            @if ($survey->end_at)
                                <div class="d-flex align-items-center gap-1 text-muted fs-12 mb-3">
                                    <i class="ti ti-clock"></i>
                                    پایان: {{ \Carbon\Carbon::parse($survey->end_at)->timezone('Asia/Tehran')->format('Y-m-d H:i') }}
                                </div>
                            @endif

                            <div class="d-grid">
                                <a href="{{ route('surveys.answer', [$group->slug, $event->slug, $survey->slug]) }}"
                                    class="btn btn-primary">
                                    <i class="ti ti-clipboard-check me-1"></i>
                                    پاسخ به نظرسنجی
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-clipboard-text fs-48 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted mb-2">هیچ نظرسنجی فعالی برای پاسخ دادن وجود ندارد</h5>
                        <p class="text-muted mb-0 fs-14">
                            در حال حاضر نظرسنجی فعالی برای شما وجود ندارد یا قبلاً پاسخ خود را ثبت کرده‌اید.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
