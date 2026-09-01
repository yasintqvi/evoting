@extends('app.layouts.app')

@section('head-tag')
    <style>
        .candidate-vote-card--selectable {
            cursor: pointer;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
            user-select: none;
        }

        .candidate-vote-card--selectable:hover {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0.25rem 0.75rem rgba(13, 110, 253, 0.12);
        }

        .candidate-vote-card--selectable.bg-success-subtle:hover {
            border-color: var(--bs-success) !important;
            box-shadow: 0 0.25rem 0.75rem rgba(25, 135, 84, 0.15);
        }

        .candidate-vote-card--selectable:focus-visible {
            outline: 2px solid var(--bs-primary);
            outline-offset: 2px;
        }

        .candidate-vote-card .form-check-input {
            width: 1.35rem;
            height: 1.35rem;
        }

        .candidate-vote-card .form-check-label {
            font-size: 1rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
        }

        .candidate-vote-select-btn {
            min-height: 44px;
            min-width: 110px;
            font-weight: 600;
        }

        .voting-sticky-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 1rem calc(0.75rem + env(safe-area-inset-bottom));
            background: rgba(255, 255, 255, 0.97);
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(8px);
        }

        .voting-sticky-bar__info {
            font-size: 0.875rem;
            color: var(--bs-secondary);
            line-height: 1.35;
        }

        .voting-sticky-bar__count {
            display: block;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--bs-dark);
        }

        .voting-mobile-guide {
            border-right: 4px solid var(--bs-primary);
        }

        .voting-mobile-guide ol {
            line-height: 1.7;
        }

        .vote-preview-stat {
            border-radius: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 0.65rem 0.5rem;
            text-align: center;
            height: 100%;
            background: #fff;
        }

        .vote-preview-stat__value {
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 0.15rem;
        }

        .vote-preview-stat__label {
            font-size: 0.72rem;
            color: var(--bs-secondary);
            line-height: 1.35;
            margin-bottom: 0;
        }

        .vote-preview-stat--success {
            border-color: rgba(25, 135, 84, 0.35);
            background: rgba(25, 135, 84, 0.06);
        }

        .vote-preview-stat--success .vote-preview-stat__value {
            color: var(--bs-success);
        }

        .vote-preview-stat--warning {
            border-color: rgba(255, 193, 7, 0.45);
            background: rgba(255, 193, 7, 0.08);
        }

        .vote-preview-stat--warning .vote-preview-stat__value {
            color: #b58100;
        }

        .vote-preview-stat--primary {
            border-color: rgba(13, 110, 253, 0.35);
            background: rgba(13, 110, 253, 0.06);
        }

        .vote-preview-stat--primary .vote-preview-stat__value {
            color: var(--bs-primary);
        }

        .vote-preview-stat--info {
            border-color: rgba(13, 202, 240, 0.35);
            background: rgba(13, 202, 240, 0.08);
        }

        .vote-preview-stat--info .vote-preview-stat__value {
            color: #0aa2c0;
        }

        #votePreviewModal .modal-body {
            padding: 1rem;
        }

        #votePreviewModal .modal-header {
            padding: 0.75rem 1rem;
        }

        #votePreviewModal .modal-footer {
            padding: 0.75rem 1rem;
            gap: 0.5rem;
        }

        @media (max-width: 575.98px) {
            #votePreviewModal .modal-footer {
                flex-wrap: nowrap;
            }

            #votePreviewModal .modal-footer .btn {
                flex: 1 1 50%;
                padding-top: 0.65rem;
                padding-bottom: 0.65rem;
            }
        }

        @media (min-width: 576px) {
            .vote-preview-stat {
                padding: 0.85rem 0.75rem;
            }

            .vote-preview-stat__value {
                font-size: 1.6rem;
            }

            .vote-preview-stat__label {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 1199.98px) {
            .voting-page-form {
                padding-bottom: 5.5rem;
            }

            .page-title-head .breadcrumb {
                display: none;
            }

            .candidate-vote-card .card-body {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                text-align: center;
                padding: 0.75rem 0.5rem;
            }

            .candidate-vote-card .candidate-vote-card__media img {
                width: 64px;
                height: 64px;
                object-fit: cover;
            }

            .candidate-vote-card .candidate-vote-card__body {
                width: 100%;
            }

            .candidate-vote-card .candidate-vote-card__body h5 {
                font-size: 0.85rem;
                margin-bottom: 0.25rem;
                line-height: 1.4;
            }

            .candidate-vote-card .candidate-vote-card__action {
                width: 100%;
                margin-top: 0 !important;
            }

            .candidate-vote-card .candidate-vote-select-btn {
                width: 100%;
                min-width: 0;
                font-size: 0.85rem;
                padding: 0.4rem 0.5rem;
            }

            .candidate-vote-card:not(.candidate-vote-card--selectable) .card-body {
                display: block;
                text-align: center;
            }

            .candidate-vote-card .form-check {
                position: relative;
            }

            .candidate-vote-card .form-check-input {
                position: absolute;
                opacity: 0;
                width: 1px;
                height: 1px;
            }

            #article88-live-summary .col-md-4 {
                flex: 0 0 33.333%;
                max-width: 33.333%;
            }

            #article88-live-summary .small {
                font-size: 0.7rem;
            }

            #article88-live-summary .fs-4 {
                font-size: 1.1rem !important;
            }
        }
    </style>
@endsection

@section('content')
    @php
        // کل سهم قابل رأی‌دهی از کنترلر (شامل وکالت و نوع انتخابات)
        $effectiveStock = $effectiveStock ?? 0;

        $directorCandidateCount =
            $directorCandidateCount ??
            (int) $election->candidates->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count();

        $article88VotePool =
            $article88VotePool ??
            ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88
                ? (float) $effectiveStock * (float) max(0, (int) $election->main_member_count)
                : 0.0);

        $presentParticipantsCount = $presentParticipantsCount ?? 0;
        $totalParticipantsInEvent = $totalParticipantsInEvent ?? 0;

        $userImpactPercent =
            ($totalEffectiveStockOfAllParticipants ?? 0) > 0
                ? round(($effectiveStock / $totalEffectiveStockOfAllParticipants) * 100, 2)
                : 0;

        $maxSelectableDirectors = (int) ($election->main_member_count ?? 0);
        $isCheckboxVote = in_array($election->type, [
            App\Enums\ElectionType::PUBLIC_JOINT,
            App\Enums\ElectionType::PRIVATE_JOINT,
        ]);
        $userVoteCapDisplay = $election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88
            ? (float) $article88VotePool
            : (float) $effectiveStock;
    @endphp
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">انتخابات {{ $election->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">خانه</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('elections.index', [$group->slug, $event->slug]) }}">انتخابات</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('elections.show', [$group->slug, $event->slug, $election->slug]) }}">{{ $election->title }}</a>
                </li>
                <li class="breadcrumb-item active">رای گیری</li>
            </ol>
        </div>
    </div>

    @if ($election->ends_at)
        <div class="alert {{ $election->isExpired() ? 'alert-danger' : 'alert-info' }} d-flex align-items-center gap-2">
            <i class="ti ti-clock fs-18"></i>
            <div>
                @if ($election->isExpired())
                    <strong>این انتخابات منقضی شده است.</strong>
                    زمان پایان:
                    {{ verta($election->ends_at)->format('Y/m/d H:i') }}
                @else
                    <strong>زمان پایان انتخابات:</strong>
                    {{ verta($election->ends_at)->format('Y/m/d H:i') }}
                    <div>
                        <small class="text-primary election-countdown"
                            data-end="{{ $election->ends_at?->toDateTimeString() }}">
                            زمان باقی‌مانده: <span class="value"></span>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <form id="votingForm" class="voting-page-form" action="{{ route('voting.store', [$group->slug, $event->slug, $election->slug]) }}"
        method="post">
        @csrf
        <input type="hidden" name="election_slug" value="{{ $election->slug }}">
        <input type="hidden" name="group_slug" value="{{ $group->slug }}">
        <input type="hidden" name="event_slug" value="{{ $event->slug }}">
        <div class="row voting-page-row">
            <div class="col-xl-4 col-lg-12 voting-sidebar-col order-xl-1 order-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset(user()->profile_image) }}" alt=""
                                class="avatar-xl rounded-circle border border-light border-2">
                            <div>
                                <h4 class="text-dark fw-medium mb-0">{{ user()->full_name }}</h4>
                                <p class="text-muted small mb-0">{{ $election->title }}</p>
                            </div>
                        </div>

                        <div class="voting-sidebar-extra d-none d-xl-block">
                            <div class="mt-3">
                                <h4 class="fs-15">اطلاعات انتخابات:</h4>
                                <div class="row mt-1 g-2">
                                    <div class="col-6">
                                        <h4 class="fw-medium mb-0">
                                            {{ $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count() }}
                                        </h4>
                                        <p class="mb-0 text-muted lh-lg"> کاندیدای هیت مدیره </p>
                                    </div>
                                    <div class="col-6">
                                        @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                            <h4 class="fw-medium mb-0">
                                                {{ $totalArticle88VotePool == floor($totalArticle88VotePool) ? number_format((int) $totalArticle88VotePool) : number_format($totalArticle88VotePool, 2) }}
                                            </h4>
                                            <p class="mb-0 text-muted lh-lg">سقف مجموع آرا (کل گروه)</p>
                                            <small class="text-muted">
                                                مجموع سهام مؤثر حاضرین
                                                ({{ number_format($totalEffectiveStockOfAllParticipants) }})
                                                × {{ (int) ($election->main_member_count ?? 0) }} عضو اصلی
                                            </small>
                                            <small class="text-muted d-block mt-1">
                                                @if ($election->ignore_stock_weight)
                                                    (سهام عادی + سهام ممتاز بدون وزن)
                                                @else
                                                    (سهام عادی + (سهام ممتاز × {{ $election->prefered_stock_weight }}))
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h4 class="fs-15">اطلاعات رای دهنده:</h4>
                                <div class="row mt-1 g-2">
                                    @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                        <div class="col-lg-4 col-6">
                                            <h4 class="fw-medium mb-0">{{ number_format($participant->normal_stock_count) }}
                                            </h4>
                                            <p class="mb-0 text-muted lh-lg">سهم عادی</p>
                                        </div>
                                        <div class="col-lg-4 col-6">
                                            <h4 class="fw-medium mb-0">{{ number_format($participant->prefered_stock_count) }}
                                            </h4>
                                            <p class="mb-0 text-muted lh-lg">سهم ممتاز</p>
                                        </div>
                                        <div class="col-lg-4 col-6">
                                            @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                <h4 class="fw-medium mb-0">{{ number_format((float) $article88VotePool) }}</h4>
                                                <p class="mb-0 text-muted lh-lg">سقف رأی قابل تخصیص</p>
                                                <small class="text-primary">
                                                    {{ number_format($effectiveStock) }} × {{ (int) ($election->main_member_count ?? 0) }} عضو اصلی
                                                </small>
                                            @else
                                                <h4 class="fw-medium mb-0">{{ number_format($effectiveStock) }}</h4>
                                                <p class="mb-0 text-muted lh-lg"> سهم شما (رأی قابل‌تخصیص)</p>
                                            @endif
                                            @if ($election->ignore_stock_weight)
                                                <small class="text-info d-block">بدون وزن</small>
                                            @endif
                                        </div>
                                        <div class="col-lg-4 col-6">
                                            <h4 class="fw-medium mb-0">{{ $userImpactPercent }}٪</h4>
                                            <p class="mb-0 text-muted lh-lg">درصد تأثیرگذاری</p>
                                            <small class="text-muted">
                                                از کل سهام حاضرین در جلسه
                                            </small>
                                        </div>
                                    @else
                                        <div class="col-lg-4 col-6">
                                            <h4 class="fw-medium mb-0">1</h4>
                                            <p class="mb-0 text-muted lh-lg">کل سهم</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-xl-none mt-3">
                            <button class="btn btn-light btn-sm w-100 d-flex align-items-center justify-content-between"
                                type="button" data-bs-toggle="collapse" data-bs-target="#votingSidebarDetails"
                                aria-expanded="false" aria-controls="votingSidebarDetails">
                                <span>جزئیات سهام و انتخابات</span>
                                <i class="ti ti-chevron-down"></i>
                            </button>
                            <div class="collapse mt-2" id="votingSidebarDetails">
                                <div class="border rounded-3 p-3 bg-light-subtle">
                                    <div class="row g-2 small">
                                        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                            <div class="col-6">
                                                <div class="text-muted">سهم عادی</div>
                                                <div class="fw-semibold">{{ number_format($participant->normal_stock_count) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-muted">سهم ممتاز</div>
                                                <div class="fw-semibold">{{ number_format($participant->prefered_stock_count) }}</div>
                                            </div>
                                        @endif
                                        <div class="col-6">
                                            <div class="text-muted">تعداد نامزدها</div>
                                            <div class="fw-semibold">
                                                {{ $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count() }}
                                            </div>
                                        </div>
                                        @if ($maxSelectableDirectors > 0)
                                            <div class="col-6">
                                                <div class="text-muted">عضو اصلی</div>
                                                <div class="fw-semibold">{{ $maxSelectableDirectors }} نفر</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="voting-sidebar-extra d-none d-xl-block">
                <div class="card mt-3 border-primary border-opacity-25">
                    <div class="card-header bg-primary bg-opacity-10 border-bottom border-dashed py-3">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2 fs-16">
                            <span
                                class="avatar-sm rounded-circle bg-primary d-inline-flex align-items-center justify-content-center">
                                <i class="ti ti-users-group text-white"></i>
                            </span>
                            حضور در این رویداد
                        </h5>
                        <p class="text-muted mb-0 mt-2 small">تعداد سهام‌داران حاضر (موکل از طریق وکالت حاضر است؛ وکیل فقط وقتی شمرده می‌شود که خودش هم سهام‌دار باشد).</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div
                                    class="rounded-3 border border-success border-opacity-25 bg-success bg-opacity-10 p-3 text-center h-100">
                                    <div class="text-success small fw-semibold mb-1">حاضر در جلسه</div>
                                    <div class="display-6 fw-bold text-success lh-1">{{ $presentParticipantsCount }}</div>
                                    <div class="text-muted small mt-2">نفر</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div
                                    class="rounded-3 border border-danger border-opacity-25 bg-danger bg-opacity-10 p-3 text-center h-100">
                                    <div class="text-danger small fw-semibold mb-1">غایب در جلسه</div>
                                    <div class="display-6 fw-bold text-danger lh-1">{{ $absentParticipantsCount ?? max(0, $totalParticipantsInEvent - $presentParticipantsCount) }}</div>
                                    <div class="text-muted small mt-2">نفر</div>
                                </div>
                            </div>
                        </div>
                        @if ($totalParticipantsInEvent > 0)
                            @php
                                $presencePercent = (int) min(
                                    100,
                                    round((100 * $presentParticipantsCount) / $totalParticipantsInEvent),
                                );
                            @endphp
                            <div class="mt-4">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small text-muted">نسبت حضور به کل سهام‌داران رویداد ({{ $totalParticipantsInEvent }} نفر)</span>
                                    <span class="fw-semibold text-primary">{{ $presencePercent }}٪</span>
                                </div>
                                <div class="progress progress-md rounded-pill">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $presencePercent }}%" aria-valuenow="{{ $presencePercent }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted small mb-0 mt-3">هنوز شرکت‌کننده‌ای در این رویداد ثبت نشده است.</p>
                        @endif
                    </div>
                </div>
                @if ($representedParticipants->isNotEmpty())
                    <div class="card mt-3 border-warning border-opacity-25">
                        <div class="card-header bg-warning bg-opacity-10 border-bottom border-dashed py-3">
                            <h5 class="card-title mb-0 d-flex align-items-center gap-2 fs-16">
                                <span
                                    class="avatar-sm rounded-circle bg-warning d-inline-flex align-items-center justify-content-center">
                                    <i class="ti ti-shield-check text-white"></i>
                                </span>
                                وکالت‌های من
                            </h5>
                            <p class="text-muted mb-0 mt-2 small">افرادی که به شما وکالت داده‌اند و شما به نمایندگی از
                                آن‌ها رأی می‌دهید.</p>
                        </div>
                        <div class="card-body pt-3 px-2">
                            <div class="list-group list-group-flush">
                                @foreach ($representedParticipants as $rep)
                                    <div class="list-group-item px-2 py-2 d-flex align-items-center gap-2">
                                        <img src="{{ asset($rep->user->profile_image) }}" alt=""
                                            class="rounded-circle border"
                                            style="width:36px;height:36px;object-fit:cover;">
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-semibold text-dark small text-truncate">
                                                {{ $rep->user->full_name }}</div>
                                            @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                                <div class="text-muted" style="font-size:11px;">
                                                    @if ($rep->normal_stock_count > 0)
                                                        <span>عادی: {{ number_format($rep->normal_stock_count) }}</span>
                                                    @endif
                                                    @if ($rep->prefered_stock_count > 0)
                                                        <span class="ms-2">ممتاز:
                                                            {{ number_format($rep->prefered_stock_count) }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning border-opacity-25">وکیل</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
            <div class="col-xl-8 col-lg-12 voting-main-col order-xl-2 order-1">
                <div class="card d-xl-none mb-3 border-primary border-opacity-25">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-muted small">رأی شما</div>
                                <div class="fs-4 fw-bold text-primary mb-0">
                                    {{ number_format($userVoteCapDisplay) }}
                                </div>
                            </div>
                            @if ($maxSelectableDirectors > 0)
                                <div class="text-end">
                                    <div class="text-muted small">حداکثر انتخاب</div>
                                    <div class="fs-4 fw-bold text-dark mb-0">{{ $maxSelectableDirectors }} نفر</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary voting-mobile-guide mb-3 d-xl-none">
                    <div class="fw-semibold mb-2">راهنمای سریع رأی‌گیری</div>
                    <ol class="mb-0 ps-3 small">
                        @if ($isCheckboxVote)
                            <li>روی نام هر کاندید بزنید تا انتخاب شود</li>
                            @if ($maxSelectableDirectors > 0)
                                <li>حداکثر {{ $maxSelectableDirectors }} نفر را می‌توانید انتخاب کنید</li>
                            @endif
                        @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                            <li>برای هر نامزد تعداد رأی را وارد کنید</li>
                            <li>مجموع آرا نباید از {{ number_format($userVoteCapDisplay) }} بیشتر شود</li>
                        @else
                            <li>نامزدهای مورد نظر خود را انتخاب کنید</li>
                        @endif
                        <li>در پایان «ثبت نهایی» را بزنید و تأیید کنید</li>
                    </ol>
                </div>

                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <h4 class="card-title mb-0">انتخاب نامزدها</h4>
                        @if ($isCheckboxVote)
                            <p class="text-muted small mb-0 mt-1">برای انتخاب، روی کارت نامزد ضربه بزنید</p>
                        @endif
                    </div>
                    @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                        <div class="card-body border-bottom bg-light py-3" id="article88-live-summary">
                            <div class="row g-3 text-center align-items-center">
                                <div class="col-md-4">
                                    <div class="small text-muted mb-1">سقف مجموع آرا (سهم × تعداد نامزد)</div>
                                    <div class="fs-4 fw-bold text-dark" id="article88-cap-display">
                                        {{ $article88VotePool == floor($article88VotePool) ? (int) $article88VotePool : number_format($article88VotePool, 2) }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted mb-1">مجموع تخصیص داده‌شده</div>
                                    <div class="fs-4 fw-bold text-secondary" id="article88-allocated-display">0</div>
                                </div>
                                <div class="col-md-4">
                                    <div class="small text-muted mb-1">باقی‌مانده</div>
                                    <div class="fs-4 fw-bold text-primary" id="article88-remaining-display">
                                        {{ $article88VotePool == floor($article88VotePool) ? (int) $article88VotePool : number_format($article88VotePool, 2) }}
                                    </div>
                                </div>
                            </div>
                            <p class="small text-muted mb-0 mt-3 text-center">می‌توانید کل سقف را به یک نامزد بدهید یا بین
                                چند نامزد تقسیم کنید؛ مجموع نباید از سقف بیشتر شود.</p>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($election->candidates->where('candidate_type', App\Enums\CandidateType::DIRECTOR) as $candidate)
                                <div class="col-6 col-xl-3">
                                    <div class="card candidate-vote-card h-100 border @if (in_array($election->type, [App\Enums\ElectionType::PUBLIC_JOINT, App\Enums\ElectionType::PRIVATE_JOINT])) candidate-vote-card--selectable @endif"
                                        data-candidate-card="{{ $candidate->id }}"
                                        @if (in_array($election->type, [App\Enums\ElectionType::PUBLIC_JOINT, App\Enums\ElectionType::PRIVATE_JOINT]))
                                            role="button" tabindex="0" title="برای انتخاب روی کارت کلیک کنید"
                                        @endif>
                                        <div class="card-body text-center">
                                            <div class="candidate-vote-card__media">
                                                <img src="{{ asset($candidate->user->profile_image) }}"
                                                    alt="" class="avatar-xl rounded">
                                            </div>
                                            <div class="candidate-vote-card__body">
                                                <h5 class="mb-0">{{ $candidate->user->full_name }}</h5>
                                                <div class="candidate-vote-badge text-success fw-semibold small mt-1 d-none"
                                                    data-vote-badge="{{ $candidate->id }}"></div>
                                            </div>
                                            <div class="candidate-vote-card__action mt-3 d-flex gap-2 justify-content-center">

                                                {{-- تعاونی: PUBLIC_JOINT --}}
                                                @if ($election->type == App\Enums\ElectionType::PUBLIC_JOINT)
                                                    <div class="form-check form-checkbox-secondary mb-0">
                                                        <input type="checkbox" class="form-check-input candidate-vote-input"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            value="1" id="candidate_{{ $candidate->id }}"
                                                            data-candidate-id="{{ $candidate->id }}">
                                                        <label for="candidate_{{ $candidate->id }}"
                                                            class="form-check-label candidate-vote-select-btn btn btn-outline-primary mb-0">انتخاب</label>
                                                    </div>

                                                    {{-- خصوصی: PRIVATE_JOINT --}}
                                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT)
                                                    <div class="form-check form-checkbox-secondary mb-0">
                                                        <input type="checkbox" class="form-check-input candidate-vote-input"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            value="{{ $effectiveStock }}"
                                                            id="candidate_{{ $candidate->id }}"
                                                            data-candidate-id="{{ $candidate->id }}">
                                                        <label for="candidate_{{ $candidate->id }}"
                                                            class="form-check-label candidate-vote-select-btn btn btn-outline-primary mb-0">انتخاب</label>
                                                    </div>

                                                    {{-- ماده ۸۸: PRIVATE_JOINT_WITH_88 --}}
                                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                    <div class="mt-2">


                                                        <input type="number"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            id="director-candidate-input-{{ $candidate->id }}"
                                                            class="form-control article88-vote-input candidate-vote-input"
                                                            min="0" step="any" inputmode="decimal" lang="en"
                                                            data-candidate-id="{{ $candidate->id }}"
                                                            data-max-hint-for="article88-max-hint-{{ $candidate->id }}"
                                                            max="{{ $article88VotePool }}" value="0"
                                                            oninput="updateVoteValue('director-candidate-input-{{ $candidate->id }}', 'director-candidate-value-{{ $candidate->id }}'); updateArticle88Totals(); updateCandidateVoteCard({{ $candidate->id }});">

                                                        <div class="d-flex justify-content-center mt-1">
                                                            <h4 id="director-candidate-value-{{ $candidate->id }}">0</h4>
                                                            <span>&nbsp; رأی</span>
                                                        </div>
                                                        <small class="text-muted d-block mt-2 px-1"
                                                            id="article88-max-hint-{{ $candidate->id }}"></small>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card d-none d-xl-block">
                    <button type="button" class="btn btn-primary btn-lg w-100" id="showVotePreviewBtn">
                        ثبت نهایی
                    </button>
                    <p class="text-muted small text-center mb-0 mt-2 px-3 pb-3">
                        اگر نمی‌خواهید به کسی رأی بدهید، بدون انتخاب کاندیدا هم می‌توانید ثبت کنید تا فقط شرکت شما در رأی‌گیری ذخیره شود.
                    </p>
                </div>

                <div class="voting-sticky-bar d-xl-none">
                    <div class="voting-sticky-bar__info">
                        <span class="voting-sticky-bar__count" id="stickySelectedCount">۰ نامزد</span>
                        @if ($maxSelectableDirectors > 0)
                            <span class="text-muted">از {{ $maxSelectableDirectors }} نفر</span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-primary btn-lg flex-shrink-0" id="showVotePreviewBtnSticky">
                        ثبت نهایی
                    </button>
                </div>

                {{-- مودال پیش‌نمایش رای قبل از ثبت --}}
                <div class="modal fade" id="votePreviewModal" tabindex="-1" aria-labelledby="votePreviewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white py-2">
                                <h5 class="modal-title fs-16" id="votePreviewModalLabel">
                                    پیش‌نمایش رای شما
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2 mb-3 vote-preview-stats">
                                    <div class="col-6">
                                        <div class="vote-preview-stat vote-preview-stat--success">
                                            <div class="vote-preview-stat__value" id="preview-voted-count">0</div>
                                            <p class="vote-preview-stat__label mb-0">
                                                <span class="d-md-none">انتخاب شده</span>
                                                <span class="d-none d-md-inline">کاندیدای رأی‌داده‌شده</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="vote-preview-stat vote-preview-stat--warning">
                                            <div class="vote-preview-stat__value" id="preview-not-voted-count">0</div>
                                            <p class="vote-preview-stat__label mb-0">
                                                <span class="d-md-none">بدون رأی</span>
                                                <span class="d-none d-md-inline">کاندیدای بدون رأی</span>
                                            </p>
                                        </div>
                                    </div>
                                    @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                        <div class="col-6">
                                            <div class="vote-preview-stat vote-preview-stat--primary">
                                                <div class="vote-preview-stat__value" id="preview-total-votes">0</div>
                                                <p class="vote-preview-stat__label mb-0">
                                                    @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                        <span class="d-md-none">مجموع رأی</span>
                                                        <span class="d-none d-md-inline">مجموع رأی تخصیص‌داده‌شده</span>
                                                    @else
                                                        <span class="d-md-none">کل رأی</span>
                                                        <span class="d-none d-md-inline">تعداد کل رای داده شده</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="vote-preview-stat vote-preview-stat--info">
                                                <div class="vote-preview-stat__value" id="preview-remaining-votes">0</div>
                                                <p class="vote-preview-stat__label mb-0">
                                                    @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                        <span class="d-md-none">باقی‌مانده</span>
                                                        <span class="d-none d-md-inline">باقی‌مانده تا سقف</span>
                                                    @else
                                                        <span class="d-md-none">رأی هر نفر</span>
                                                        <span class="d-none d-md-inline">کل سهم شما (برای هر کاندیدا)</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div id="abstain-preview-alert" class="alert alert-secondary py-2 px-3 small mb-3 d-none">
                                    <strong>شرکت بدون رأی:</strong>
                                    هیچ کاندیدایی انتخاب نشده. با تأیید، فقط حضور شما ثبت می‌شود و رأیی داده نمی‌شود.
                                    <span class="d-none d-md-inline">این عمل قابل ویرایش نیست.</span>
                                </div>

                                @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                    <div class="alert alert-info small py-2 px-3 mb-3 d-none d-md-block">
                                        <strong>توضیح:</strong>
                                        سقف رأی شما = کل سهم × تعداد اعضای اصلی. مجموع آرا نباید از سقف بیشتر شود.
                                    </div>
                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT)
                                    <div class="alert alert-info small py-2 px-3 mb-3 d-none d-md-block">
                                        <strong>توضیح:</strong>
                                        با انتخاب هر کاندید، همان کل سهم شما برای او ثبت می‌شود.
                                    </div>
                                @endif

                                <div class="alert alert-warning py-2 px-3 mb-0 small">
                                    بعد از ثبت امکان ویرایش وجود ندارد.
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center border-top">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                    انصراف
                                </button>
                                <button type="button" id="confirmSubmitBtn" class="btn btn-success">
                                    بله، ثبت شود
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- مودال نمایش نتیجه رای‌گیری --}}
                @if (session('vote_summary'))
                    @php
                        $summary = session('vote_summary');
                    @endphp
                    <div class="modal fade" id="voteResultModal" tabindex="-1" aria-labelledby="voteResultModalLabel"
                        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="voteResultModalLabel">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        رای شما با موفقیت ثبت شد
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card border border-success border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-success fw-bold mb-2">
                                                        {{ $summary['voted_candidates_count'] }}
                                                    </h3>
                                                    <p class="mb-0 text-muted">کاندیدایی که رای داده‌اید</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border border-warning border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-warning fw-bold mb-2">
                                                        {{ $summary['not_voted_candidates_count'] }}
                                                    </h3>
                                                    <p class="mb-0 text-muted">کاندیدایی که رای نداده‌اید</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                            <div class="col-md-6">
                                                <div class="card border border-primary border-2 bg-light">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-primary fw-bold mb-2">
                                                            {{ number_format($summary['total_votes_given']) }}
                                                        </h3>
                                                        <p class="mb-0 text-muted">
                                                            @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                                مجموع رأی تخصیص‌داده‌شده
                                                            @else
                                                                تعداد کل رای داده شده
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border border-info border-2 bg-light">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-info fw-bold mb-2">
                                                            {{ number_format($summary['remaining_stock']) }}
                                                        </h3>
                                                        <p class="mb-0 text-muted">
                                                            @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                                باقی‌مانده تا سقف (سهم × نامزد)
                                                            @else
                                                                رای باقی‌مانده
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12">
                                                </line>
                                                <line x1="12" y1="8" x2="12.01" y2="8">
                                                </line>
                                            </svg>
                                            <p class="mb-0">رای شما با موفقیت ثبت شد و امکان ویرایش وجود ندارد.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <a href="{{ route('app.index') }}" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                        بازگشت به داشبورد
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        window.isArticle88Voting = @json($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88);
        window.article88VotePoolValue = {{ (float) ($article88VotePool ?? 0) }};

        function parseLocaleNumberToFloat(val) {
            if (val === null || val === undefined) {
                return 0;
            }
            var s = String(val).trim();
            if (!s) {
                return 0;
            }
            var fa = ['\u06F0', '\u06F1', '\u06F2', '\u06F3', '\u06F4', '\u06F5', '\u06F6', '\u06F7', '\u06F8', '\u06F9'];
            var faU = ['\u0660', '\u0661', '\u0662', '\u0663', '\u0664', '\u0665', '\u0666', '\u0667', '\u0668', '\u0669'];
            for (var i = 0; i < 10; i++) {
                s = s.split(fa[i]).join(String(i));
                s = s.split(faU[i]).join(String(i));
            }
            s = s.replace(/[,،٬\s\u200c\u200f]/g, '');
            s = s.replace(/[^\d.\-]/g, '');
            var n = parseFloat(s);
            return isNaN(n) ? 0 : n;
        }

        function updateArticle88Totals() {
            if (!window.isArticle88Voting) {
                return;
            }
            const form = document.getElementById('votingForm');
            if (!form) {
                return;
            }
            const pool = window.article88VotePoolValue;
            const inputs = Array.from(form.querySelectorAll('input.article88-vote-input'));
            let sum = 0;
            inputs.forEach(function(inp) {
                sum += parseLocaleNumberToFloat(inp.value);
            });
            const allocEl = document.getElementById('article88-allocated-display');
            const remEl = document.getElementById('article88-remaining-display');
            if (allocEl) {
                allocEl.textContent = sum.toLocaleString('fa-IR');
            }
            if (remEl) {
                const r = pool - sum;
                remEl.textContent = r.toLocaleString('fa-IR');
                remEl.classList.toggle('text-danger', r < 0);
                remEl.classList.toggle('text-success', r === 0 && sum > 0);
                remEl.classList.toggle('text-primary', r > 0);
            }
            inputs.forEach(function(inp) {
                const self = parseLocaleNumberToFloat(inp.value);
                const sumWithoutSelf = sum - self;
                const maxForThis = Math.max(0, pool - sumWithoutSelf);
                const hintId = inp.getAttribute('data-max-hint-for');
                const el = hintId ? document.getElementById(hintId) : null;
                if (el) {
                    el.textContent = 'اکنون حداکثر ' + maxForThis.toLocaleString('fa-IR') +
                        ' رأی می‌توانید به این نامزد بدهید (می‌توانید کل سقف را به یک نفر بدهید یا بین همه تقسیم کنید).';
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const showPreviewBtn = document.getElementById('showVotePreviewBtn');
            const showPreviewBtnSticky = document.getElementById('showVotePreviewBtnSticky');
            const confirmBtn = document.getElementById('confirmSubmitBtn');
            const form = document.getElementById('votingForm');
            const previewModalEl = document.getElementById('votePreviewModal');

            if ((!showPreviewBtn && !showPreviewBtnSticky) || !confirmBtn || !form || !previewModalEl) {
                console.error('Voting elements not found');
                return;
            }

            const previewModal = new bootstrap.Modal(previewModalEl);

            const totalCandidates =
                {{ $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count() }};

            const isArticle88 = window.isArticle88Voting;
            const article88Pool = window.article88VotePoolValue;
            const effectiveStockOnly = {{ (float) ($effectiveStock ?? 0) }};

            function poolCapForPreview() {
                return isArticle88 ? article88Pool : (effectiveStockOnly || 1);
            }

            function calculateVotePreview() {

                const inputs = form.querySelectorAll('input[name^="director_candidates"]');

                let votedCount = 0;
                let totalVotesGiven = 0;

                inputs.forEach(input => {

                    if (input.type === 'checkbox' && input.checked) {
                        votedCount++;
                        totalVotesGiven += parseFloat(input.value) || 1;
                    }

                    if (input.type === 'number') {
                        const value = isArticle88 ? parseLocaleNumberToFloat(input.value) :
                            (parseFloat(input.value) || 0);
                        if (value > 0) {
                            votedCount++;
                            totalVotesGiven += value;
                        }
                    }
                });

                const notVotedCount = totalCandidates - votedCount;
                const remainingVotes = poolCapForPreview() - totalVotesGiven;

                document.getElementById('preview-voted-count').textContent = votedCount;
                document.getElementById('preview-not-voted-count').textContent = notVotedCount;

                const abstainAlert = document.getElementById('abstain-preview-alert');
                if (abstainAlert) {
                    abstainAlert.classList.toggle('d-none', votedCount > 0);
                }

                @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                    document.getElementById('preview-total-votes').textContent =
                        totalVotesGiven.toLocaleString('fa-IR');

                    const remEl = document.getElementById('preview-remaining-votes');
                    if (isArticle88) {
                        remEl.textContent = remainingVotes.toLocaleString('fa-IR');
                        remEl.classList.toggle('text-danger', remainingVotes < 0);
                        remEl.classList.toggle('text-info', remainingVotes >= 0);
                    } else {
                        // در رأی چک‌باکسی، هر انتخاب = کل سهم؛ عدد باقی‌مانده معنا ندارد.
                        remEl.textContent = effectiveStockOnly.toLocaleString('fa-IR');
                        remEl.classList.remove('text-danger');
                        remEl.classList.add('text-info');
                    }
                @endif
            }

            const maxDirectorCandidatesForArticle88 = Number(
                "{{ (int) ($election->main_member_count ?? 0) }}");

            if (showPreviewBtn) {
                showPreviewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openVotePreview();
                });
            }

            if (showPreviewBtnSticky) {
                showPreviewBtnSticky.addEventListener('click', function(e) {
                    e.preventDefault();
                    openVotePreview();
                });
            }

            function openVotePreview() {
                calculateVotePreview();
                if (isArticle88 && article88Pool >= 0) {
                    const inputs = form.querySelectorAll('input[name^="director_candidates"]');
                    let sum = 0;
                    let votedCandidatesCount = 0;
                    inputs.forEach(function(input) {
                        if (input.type === 'number') {
                            const value = parseLocaleNumberToFloat(input.value);
                            sum += value;
                            if (value > 0) {
                                votedCandidatesCount++;
                            }
                        }
                    });
                    if (maxDirectorCandidatesForArticle88 > 0 && votedCandidatesCount >
                        maxDirectorCandidatesForArticle88) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا...',
                                text: `شما نمی‌توانید بیشتر از ${maxDirectorCandidatesForArticle88} کاندیدای اصلی انتخاب کنید.`,
                                confirmButtonText: 'باشه'
                            });
                        } else {
                            alert(
                                `شما نمی‌توانید بیشتر از ${maxDirectorCandidatesForArticle88} کاندیدای اصلی انتخاب کنید.`
                            );
                        }
                        return;
                    }
                    if (sum > article88Pool) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'بیش از سقف مجاز',
                                text: 'مجموع آرا نمی‌تواند بیش از ' + article88Pool.toLocaleString(
                                        'fa-IR') +
                                    ' (سهم شما × تعداد نامزدها) باشد.',
                                confirmButtonText: 'باشه'
                            });
                        } else {
                            alert('مجموع آرا بیش از سقف مجاز است.');
                        }
                        return;
                    }
                }
                previewModal.show();
            }

            confirmBtn.addEventListener('click', function(e) {
                e.preventDefault();

                console.log('Submitting form to:', form.action);

                previewModal.hide();

                form.submit();
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxDirectorCandidates = Number("{{ (int) ($election->main_member_count ?? 0) }}");

            const directorCheckboxes = document.querySelectorAll('input[name^="director_candidates"]');

            function enforceLimit(checkboxes, max) {
                if (!checkboxes.length || max <= 0) {
                    return;
                }
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                        if (checkedCount > max) {
                            Swal.fire({
                                icon: "error",
                                title: "خطا...",
                                text: `شما نمی‌توانید بیشتر از ${max} کاندیدای اصلی انتخاب کنید.`,
                                confirmButtonText: "باشه"
                            });
                            this.checked = false;
                        }

                        const candidateId = this.getAttribute('data-candidate-id');
                        if (candidateId && typeof updateCandidateVoteCard === 'function') {
                            updateCandidateVoteCard(candidateId);
                        }
                    });
                });
            }

            enforceLimit(directorCheckboxes, maxDirectorCandidates);

        });
    </script>

    <script>
        function updateRangeValue(rangeId, valueId) {
            var rangeElement = document.getElementById(rangeId);
            var valueElement = document.getElementById(valueId);

            if (rangeElement && valueElement) {
                valueElement.textContent = rangeElement.value;
            }
        }

        function updateVoteValue(inputId, valueId) {
            var inputElement = document.getElementById(inputId);
            var valueElement = document.getElementById(valueId);

            if (inputElement && valueElement) {
                if (window.isArticle88Voting) {
                    valueElement.textContent = String(parseLocaleNumberToFloat(inputElement.value));
                } else {
                    valueElement.textContent = inputElement.value || 0;
                }
            }
        }

        function updateStickySelectionCount() {
            const stickyEl = document.getElementById('stickySelectedCount');
            if (!stickyEl) {
                return;
            }

            const inputs = document.querySelectorAll('#votingForm input[name^="director_candidates"]');
            let selectedCount = 0;

            inputs.forEach(function(input) {
                if (input.type === 'checkbox' && input.checked) {
                    selectedCount++;
                } else if (input.type === 'number') {
                    const value = window.isArticle88Voting ?
                        parseLocaleNumberToFloat(input.value) :
                        (parseFloat(input.value) || 0);
                    if (value > 0) {
                        selectedCount++;
                    }
                }
            });

            stickyEl.textContent = selectedCount.toLocaleString('fa-IR') + ' نامزد';
        }

        function updateCandidateVoteCard(candidateId) {
            const card = document.querySelector('[data-candidate-card="' + candidateId + '"]');
            const badge = document.querySelector('[data-vote-badge="' + candidateId + '"]');
            const input = document.querySelector('.candidate-vote-input[data-candidate-id="' + candidateId + '"]');

            if (!card || !input) {
                return;
            }

            let selected = false;
            let voteValue = 0;

            if (input.type === 'checkbox') {
                selected = input.checked;
            } else {
                voteValue = window.isArticle88Voting ?
                    parseLocaleNumberToFloat(input.value) :
                    (parseFloat(input.value) || 0);
                selected = voteValue > 0;
            }

            card.classList.toggle('border-success', selected);
            card.classList.toggle('border-2', selected);
            card.classList.toggle('bg-success-subtle', selected);

            const label = card.querySelector('.form-check-label');
            if (label) {
                label.textContent = selected ? 'انتخاب شد' : 'انتخاب';
                label.classList.toggle('btn-outline-primary', !selected);
                label.classList.toggle('btn-success', selected);
                label.classList.toggle('text-white', selected);
            }

            if (badge) {
                if (selected && window.isArticle88Voting) {
                    badge.textContent = voteValue.toLocaleString('fa-IR') + ' رأی';
                    badge.classList.remove('d-none');
                } else if (selected && input.type === 'checkbox') {
                    badge.textContent = 'انتخاب شد ✓';
                    badge.classList.remove('d-none');
                } else {
                    badge.textContent = '';
                    badge.classList.add('d-none');
                }
            }

            updateStickySelectionCount();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.candidate-vote-input').forEach(function(input) {
                const candidateId = input.getAttribute('data-candidate-id');
                if (!candidateId) {
                    return;
                }

                input.addEventListener('change', function() {
                    updateCandidateVoteCard(candidateId);
                });

                updateCandidateVoteCard(candidateId);
            });

            // کلیک روی کل کارت = انتخاب/لغو انتخاب (فقط برای چک‌باکس)
            document.querySelectorAll('.candidate-vote-card--selectable').forEach(function(card) {
                function toggleCardSelection(e) {
                    if (e.target.closest('input, label, a, button, .form-check')) {
                        return;
                    }

                    const input = card.querySelector('input.candidate-vote-input[type="checkbox"]');
                    if (!input || input.disabled) {
                        return;
                    }

                    e.preventDefault();
                    input.checked = !input.checked;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }

                card.addEventListener('click', toggleCardSelection);
                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleCardSelection(e);
                    }
                });
            });

            updateStickySelectionCount();
        });

        document.addEventListener('DOMContentLoaded', function() {
            // به‌روزرسانی مقادیر اولیه برای input های number
            @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                @foreach ($election->candidates->where('candidate_type', App\Enums\CandidateType::DIRECTOR) as $candidate)
                    var inputId = 'director-candidate-input-{{ $candidate->id }}';
                    var valueId = 'director-candidate-value-{{ $candidate->id }}';
                    updateVoteValue(inputId, valueId);
                    updateCandidateVoteCard({{ $candidate->id }});
                @endforeach
                updateArticle88Totals();
            @endif
        });
    </script>

    @if (session('vote_summary'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // نمایش مودال نتیجه رای‌گیری
                var voteResultModal = new bootstrap.Modal(document.getElementById('voteResultModal'));
                voteResultModal.show();
            });
        </script>
    @endif
    <script>
        (function() {
            function formatDuration(ms) {
                if (ms <= 0) return '0:00:00';
                var totalSeconds = Math.floor(ms / 1000);
                var days = Math.floor(totalSeconds / 86400);
                totalSeconds %= 86400;
                var hours = Math.floor(totalSeconds / 3600);
                var minutes = Math.floor((totalSeconds % 3600) / 60);
                var seconds = totalSeconds % 60;
                if (days > 0) {
                    return days + ' روز ' + String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') +
                        ':' + String(seconds).padStart(2, '0');
                }
                return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds)
                    .padStart(2, '0');
            }

            function tick() {
                var items = document.querySelectorAll('.election-countdown');
                var now = new Date().getTime();
                items.forEach(function(el) {
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
