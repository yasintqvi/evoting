@extends('app.layouts.app')

@section('head-tag')
    <style>
        .my-election-card__cta {
            min-height: 46px;
            font-weight: 600;
        }

        .my-election-card__details {
            border: 1px dashed rgba(0, 0, 0, 0.1);
            border-radius: 0.65rem;
            background: rgba(0, 0, 0, 0.02);
            padding: 0.75rem;
        }

        .my-election-card__stats {
            display: flex;
            gap: 0.5rem;
        }

        .my-election-card__stat {
            flex: 1;
            text-align: center;
            padding: 0.5rem 0.35rem;
            border: 1px dashed rgba(0, 0, 0, 0.08);
            border-radius: 0.5rem;
            background: #fff;
        }

        .my-election-card__stat-value {
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1.2;
        }

        @media (max-width: 767.98px) {
            .my-election-card .card-header {
                padding: 0.85rem 1rem;
            }

            .my-election-card .card-body {
                padding: 0.85rem 1rem 1rem;
            }

            .my-election-card__meta {
                font-size: 0.8rem;
            }

            .my-election-card__details {
                padding: 0.65rem;
            }

            .my-election-card__stat-value {
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
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

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="#active-surveys" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                فعال
                <span class="badge bg-info ms-1">{{ $availableSurveys->count() }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#past-surveys" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                گذشته
                <span class="badge bg-secondary ms-1">{{ $pastSurveys->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane show active" id="active-surveys" role="tabpanel">
            @if ($availableSurveys->count() > 0)
                <div class="row g-3">
                    @foreach ($availableSurveys as $item)
                        @php
                            $survey  = $item['survey'];
                            $event   = $item['event'];
                            $itemGroup   = $item['group'];
                        @endphp
                        <div class="col-12 col-xl-4">
                            <div class="card border border-dashed h-100 my-election-card">
                                <div class="card-header border-bottom border-dashed bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="min-w-0">
                                            <p class="text-muted mb-1 fs-13 fw-semibold my-election-card__meta d-flex align-items-center gap-2">
                                                <span>{{ $itemGroup->title }}</span>
                                                <span class="text-muted fw-normal">· {{ verta($survey->created_at)->format('Y/m/d') }}</span>
                                            </p>
                                            <h5 class="card-title mb-1 fw-semibold">{{ $survey->title }}</h5>
                                        </div>
                                        <span class="badge badge-soft-success flex-shrink-0">فعال</span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="my-election-card__details mb-3">
                                        <div class="my-election-card__stats mb-2">
                                            <div class="my-election-card__stat">
                                                <div class="my-election-card__stat-value">{{ $survey->questions->count() }}</div>
                                                <small class="text-muted">تعداد سوال</small>
                                            </div>
                                        </div>

                                        @if ($survey->end_at)
                                            <div class="d-flex align-items-center gap-1 text-muted fs-12">
                                                <i class="ti ti-clock"></i>
                                                پایان: {{ \Carbon\Carbon::parse($survey->end_at)->timezone('Asia/Tehran')->format('Y-m-d H:i') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-grid mt-auto">
                                        <a href="{{ route('surveys.answer', [$itemGroup->slug, $event->slug, $survey->slug]) }}"
                                            class="btn btn-primary btn-lg my-election-card__cta">
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
        </div>

        <div class="tab-pane" id="past-surveys" role="tabpanel">
            @if ($pastSurveys->count() > 0)
                <div class="row g-3">
                    @foreach ($pastSurveys as $item)
                        @php
                            $survey  = $item['survey'];
                            $event   = $item['event'];
                            $itemGroup   = $item['group'];
                        @endphp
                        <div class="col-12 col-xl-4">
                            <div class="card border border-dashed h-100 my-election-card">
                                <div class="card-header border-bottom border-dashed">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="min-w-0">
                                            <h5 class="card-title mb-1 fw-semibold">{{ $survey->title }}</h5>
                                            <p class="text-muted mb-0 fs-13 my-election-card__meta">
                                                {{ $itemGroup->title }}
                                            </p>
                                        </div>
                                        <span class="badge {{ $item['has_answered'] ? 'badge-soft-success' : 'badge-soft-secondary' }} flex-shrink-0">
                                            {{ $item['has_answered'] ? 'پاسخ داده شده' : 'پایان یافته' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="my-election-card__details mb-3">
                                        <div class="small fw-semibold mb-1">سوال</div>
                                        <p class="text-muted fs-13 mb-0">
                                            {{ $survey->questions->first()->question_text ?? '-' }}
                                        </p>

                                        @if ($survey->end_at)
                                            <div class="d-flex align-items-center gap-1 text-muted fs-12 mt-2">
                                                <i class="ti ti-clock"></i>
                                                پایان: {{ \Carbon\Carbon::parse($survey->end_at)->timezone('Asia/Tehran')->format('Y-m-d H:i') }}
                                            </div>
                                        @endif
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
                                <i class="ti ti-clipboard-off fs-48 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted mb-2">نظرسنجی گذشته‌ای یافت نشد</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
