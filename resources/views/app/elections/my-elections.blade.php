@extends('app.layouts.app')

@section('head-tag')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">
    <style>
        @media (max-width: 767.98px) {
            .my-elections-page .page-title-head .breadcrumb {
                display: none;
            }

            #electionFilters.collapse:not(.show) {
                display: none;
            }
        }

        @media (min-width: 768px) {
            #electionFilters.collapse {
                display: block;
                visibility: visible;
            }
        }

        .my-elections-filter-card .card-header {
            padding: 0.75rem 1rem;
        }

        .my-elections-filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding-top: 0.25rem;
            border-top: 1px dashed rgba(0, 0, 0, 0.08);
            margin-top: 0.25rem;
        }

        @media (min-width: 992px) {
            .my-elections-filter-actions {
                border-top: 0;
                margin-top: 0;
                padding-top: 0;
                justify-content: flex-end;
            }
        }

        .my-elections-tabs .nav-link {
            font-size: 0.875rem;
            padding: 0.65rem 0.75rem;
        }

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

        .my-election-card__candidate {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.45rem;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            max-width: 100%;
        }

        .my-election-card__candidate img {
            width: 22px;
            height: 22px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .my-election-card__candidate span {
            font-size: 0.75rem;
            color: var(--bs-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .my-election-past-candidates {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
        }

        .my-election-past-candidate {
            flex: 0 0 calc(33.333% - 0.35rem);
            max-width: calc(33.333% - 0.35rem);
            min-width: 88px;
            text-align: center;
        }

        .my-election-past-candidate img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            display: block;
            margin: 0 auto;
        }

        .my-election-past-candidate p {
            font-size: 0.75rem;
            margin-bottom: 0;
            margin-top: 0.35rem;
            line-height: 1.35;
            word-break: break-word;
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

            .my-election-past-candidate {
                flex: 0 0 calc(50% - 0.35rem);
                max-width: calc(50% - 0.35rem);
            }
        }
    </style>
@endsection

@section('content')
    <div class="my-elections-page">
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">{{ $title ?? 'انتخابات من' }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
                <li class="breadcrumb-item active">{{ $title ?? 'انتخابات من' }}</li>
            </ol>
        </div>
    </div>

    <!-- فرم فیلتر -->
    <div class="card mb-3 my-elections-filter-card">
        <div class="card-header bg-transparent border-bottom d-none d-md-flex">
            <span class="fw-semibold mb-0">
                <i class="ti ti-filter me-1"></i>
                فیلتر انتخابات
            </span>
        </div>
        <button class="card-header bg-transparent border-bottom d-md-none w-100 text-start py-3"
            type="button" data-bs-toggle="collapse" data-bs-target="#electionFilters"
            aria-expanded="{{ request()->hasAny(['status', 'start_date', 'end_date']) ? 'true' : 'false' }}"
            aria-controls="electionFilters">
            <div class="d-flex align-items-center justify-content-between">
                <span class="fw-semibold">
                    <i class="ti ti-filter me-1"></i>
                    فیلتر و جستجو
                </span>
                <i class="ti ti-chevron-down"></i>
            </div>
        </button>
        <div class="collapse @if (request()->hasAny(['status', 'start_date', 'end_date'])) show @endif" id="electionFilters">
            <div class="card-body pt-3 pb-3">
                <form method="GET" action="{{ route('my-elections.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label class="form-label mb-1">وضعیت</label>
                            <select name="status" class="form-select">
                                <option value="">همه</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>در حال برگزاری</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منقضی شده</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تمام شده</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>لغو شده</option>
                            </select>
                        </div>

                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label mb-1">از تاریخ</label>
                            <input type="text" name="start_date" class="form-control" data-jdp
                                value="{{ request('start_date') }}" placeholder="انتخاب تاریخ" autocomplete="off">
                        </div>

                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label mb-1">تا تاریخ</label>
                            <input type="text" name="end_date" class="form-control" data-jdp
                                value="{{ request('end_date') }}" placeholder="انتخاب تاریخ" autocomplete="off">
                        </div>
                    </div>

                    <div class="my-elections-filter-actions">
                        <button type="submit" class="btn btn-primary flex-fill flex-sm-grow-0">
                            <i class="ti ti-filter me-1"></i>
                            اعمال فیلتر
                        </button>
                        <a href="{{ route('my-elections.index') }}" class="btn btn-light flex-fill flex-sm-grow-0">
                            <i class="ti ti-refresh me-1"></i>
                            پاک کردن
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3 my-elections-tabs" role="tablist">
        <li class="nav-item flex-fill" role="presentation">
            <a href="#home" data-bs-toggle="tab" aria-expanded="true" class="nav-link active text-center" aria-selected="true"
                role="tab">
                <span class="d-md-none">فعال</span>
                <span class="d-none d-md-inline">انتخابات در حال برگزاری</span>
                <span class="badge bg-danger ms-1">{{ $availableElections->count() }}</span>
            </a>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <a href="#profile" data-bs-toggle="tab" aria-expanded="false" class="nav-link text-center" aria-selected="false"
                tabindex="-1" role="tab">
                <span class="d-md-none">گذشته</span>
                <span class="d-none d-md-inline">انتخابات گذشته</span>
                <span class="badge bg-success ms-1">{{ $unavailableElections->count() }}</span>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        {{-- <div class="tab-pane show active mb-4 " id="home" role="tabpanel">
            @if ($availableElections->count() > 0)
                <div class="row">
                    @foreach ($availableElections as $item)
                        @php
                            $election = $item['election'];
                            $event = $item['event'];
                            $group = $item['group'];
                            $participant = $item['participant'];
                        @endphp
                        <div class="col-xl-4 col-lg-12">
                            <div class="card border border-dashed h-100">
                                <div class="card-header border-bottom border-dashed">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 fw-semibold">{{ $election->title }}</h5>
                                            <p class="text-muted mb-0 fs-13">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="me-1">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z">
                                                    </path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                </svg>
                                                {{ $event->title }} - {{ $group->title }}
                                            </p>
                                        </div>
                                        <span class="badge badge-soft-success">در حال برگزاری</span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 border border-dashed rounded bg-light">
                                                <h4 class="mb-0 text-dark fw-bold">
                                                    {{ $election->candidates->count() }}
                                                </h4>
                                                <small class="text-muted">تعداد کاندیدا</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 border border-dashed rounded bg-light">
                                                <h4 class="mb-0 text-dark fw-bold">
                                                    {{ $election->main_member_count }}
                                                </h4>
                                                <small class="text-muted">عضو اصلی</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="fs-14 fw-semibold mb-2">کاندیداها:</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($election->candidates->take(3) as $candidate)
                                                <div class="d-flex align-items-center gap-1">
                                                    <img src="{{ asset($candidate->user->profile_image) }}"
                                                        alt="{{ $candidate->user->full_name }}"
                                                        class="avatar-xs rounded-circle">
                                                    <small class="text-muted">{{ $candidate->user->full_name }}</small>
                                                </div>
                                            @endforeach
                                            @if ($election->candidates->count() > 3)
                                                <small class="text-muted">+{{ $election->candidates->count() - 3 }} نفر
                                                    دیگر</small>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <a href="{{ route('voting.create', [$group->slug, $event->slug, $election->slug]) }}"
                                            class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            مشارکت
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="text-muted mb-3">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <h5 class="text-muted mb-2">هیچ انتخابات فعالی برای رای دادن وجود ندارد</h5>
                                <p class="text-muted mb-0 fs-14">
                                    در حال حاضر انتخابات در حال برگزاری برای شما وجود ندارد یا قبلاً رای خود را ثبت
                                    کرده‌اید.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div> --}}

        <div class="tab-pane show active mb-4 " id="home" role="tabpanel">
            @if ($availableElections->count() > 0)
                <div class="row g-3">
                    @foreach ($availableElections as $item)
                        @php
                            $election = $item['election'];
                            $event = $item['event'];
                            $group = $item['group'];
                            $participant = $item['participant'];
                        @endphp
                        <div class="col-12 col-xl-4">
                            <div class="card border border-dashed h-100 my-election-card">
                                <div class="card-header border-bottom border-dashed bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="min-w-0">
                                            <h5 class="card-title mb-1 fw-semibold">{{ $election->title }}</h5>
                                            <p class="text-muted mb-1 fs-13 my-election-card__meta">
                                                {{ $event->title }} · {{ $group->title }}
                                            </p>
                                            <small class="text-muted my-election-card__meta">
                                                {{ verta($election->created_at)->format('Y/m/d') }}
                                            </small>
                                        </div>
                                        <span class="badge badge-soft-success flex-shrink-0">فعال</span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <div class="my-election-card__details mb-3">
                                        <div class="my-election-card__stats mb-2">
                                            <div class="my-election-card__stat">
                                                <div class="my-election-card__stat-value">{{ $election->candidates->count() }}</div>
                                                <small class="text-muted">کاندیدا</small>
                                            </div>
                                            <div class="my-election-card__stat">
                                                <div class="my-election-card__stat-value">{{ $election->main_member_count }}</div>
                                                <small class="text-muted">عضو اصلی</small>
                                            </div>
                                        </div>

                                        <div class="small fw-semibold mb-2">کاندیداها</div>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($election->candidates->take(4) as $candidate)
                                                <div class="my-election-card__candidate">
                                                    <img src="{{ asset($candidate->user->profile_image) }}"
                                                        alt="{{ $candidate->user->full_name }}"
                                                        class="rounded-circle">
                                                    <span>{{ $candidate->user->full_name }}</span>
                                                </div>
                                            @endforeach
                                            @if ($election->candidates->count() > 4)
                                                <div class="my-election-card__candidate">
                                                    <span>+{{ $election->candidates->count() - 4 }} نفر دیگر</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-grid mt-auto">
                                        <a href="{{ route('voting.create', [$group->slug, $event->slug, $election->slug]) }}"
                                            class="btn btn-primary btn-lg my-election-card__cta">
                                            <i class="ti ti-checkbox me-1"></i>
                                            رأی بدهید
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <h5 class="text-muted mb-2">هیچ انتخابات فعالی برای رای دادن وجود ندارد</h5>
                                <p class="text-muted mb-0 fs-14">
                                    در حال حاضر انتخابات در حال برگزاری برای شما وجود ندارد یا قبلاً رای خود را ثبت
                                    کرده‌اید.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>


        <div class="tab-pane mb-4" id="profile" role="tabpanel">
            @if ($unavailableElections->count() > 0)
                <div class="row g-3">
                    @foreach ($unavailableElections as $item)
                        @php
                            $election = $item['election'];
                            $event = $item['event'];
                            $group = $event->group;
                            $participant = $item['participant'];
                        @endphp
                        <div class="col-12 col-xl-4 mb-2">
                            <div class="card border border-dashed h-100 my-election-card">
                                <div class="card-header border-bottom border-dashed">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="min-w-0">
                                            <h5 class="card-title mb-1 fw-semibold">{{ $election->title }}</h5>
                                            <p class="text-muted mb-0 fs-13 my-election-card__meta">
                                                {{ $event->title }} · {{ $group->title }}
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            <span class="badge badge-soft-info">{{ $item['election']->status->toFa() }}</span>
                                            @if ($election->isFinished())
                                                <form action="{{ route('my-elections.destroy', $election) }}"
                                                    method="POST" class="mb-0"
                                                    onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این انتخابات را از لیست خود حذف کنید؟')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $participantVotes = $participant->votes;
                                        $votedCount = $participantVotes
                                            ->filter(fn ($vote) => $vote->candidate_id && $vote->vote_count > 0)
                                            ->count();
                                    @endphp
                                    <div class="my-election-card__details">
                                        <div class="my-election-card__stats mb-2">
                                            <div class="my-election-card__stat">
                                                <div class="my-election-card__stat-value">{{ $votedCount }}</div>
                                                <small class="text-muted">رأی داده‌شده</small>
                                            </div>
                                            <div class="my-election-card__stat">
                                                <div class="my-election-card__stat-value">{{ $election->candidates->count() - $votedCount }}</div>
                                                <small class="text-muted">بدون رأی</small>
                                            </div>
                                        </div>

                                        <div class="small fw-semibold mb-2">نتیجه رأی شما</div>
                                        <div class="my-election-past-candidates">
                                        @foreach ($election->candidates as $candidate)
                                            @php
                                                $candidateUser = $candidate?->user;
                                                $candidateVote = $participantVotes->where(
                                                    'candidate_id',
                                                    $candidate->id,
                                                );
                                            @endphp
                                            @if ($candidateVote->count() > 0)
                                                <div class="my-election-past-candidate d-flex flex-column justify-content-center align-items-center border border-success rounded p-2 bg-success-subtle">
                                                    <img src="{{ asset($candidateUser?->profile_image) }}"
                                                        alt="image" class="rounded-circle">
                                                    <p class="fw-semibold mt-1">
                                                        {{ $candidateUser?->full_name }}
                                                    </p>
                                                    <div class="d-flex align-items-center gap-1 text-success">
                                                        <i class="ti ti-circle-check"></i>
                                                        <span class="fw-bold">{{ $candidateVote->first()?->vote_count }}</span>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="my-election-past-candidate d-flex flex-column justify-content-center align-items-center border rounded p-2 opacity-75">
                                                    <img src="{{ asset($candidateUser?->profile_image) }}"
                                                        alt="image" class="rounded-circle">
                                                    <p class="mt-1">
                                                        {{ $candidateUser?->full_name }}
                                                    </p>
                                                </div>
                                            @endif
                                        @endforeach
                                        </div>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                <h5 class="text-muted mb-2">هیچ انتخاباتی یافت نشد</h5>
                                <p class="text-muted mb-0 fs-14">
                                    هیچ مشارکتی از سمت شما تاکنون صورت نگرفته است.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-switch tab based on status filter
            const status = '{{ request('status') }}';
            if (status && status !== 'ongoing') {
                const profileTab = document.querySelector('a[href="#profile"]');
                const homeTab = document.querySelector('a[href="#home"]');
                const profilePane = document.getElementById('profile');
                const homePane = document.getElementById('home');
                if (profileTab && homeTab && profilePane && homePane) {
                    homeTab.classList.remove('active');
                    homeTab.setAttribute('aria-selected', 'false');
                    homePane.classList.remove('show', 'active');
                    profileTab.classList.add('active');
                    profileTab.setAttribute('aria-selected', 'true');
                    profilePane.classList.add('show', 'active');
                }
            }

            // Datepicker
            if (typeof jalaliDatepicker !== 'undefined') {
                jalaliDatepicker.startWatch();
            }
        });
    </script>
@endsection
