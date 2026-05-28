@extends('app.layouts.app')

@section('head-tag')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
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
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('my-elections.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>در حال برگزاری
                            </option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منقضی شده
                            </option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تمام شده
                            </option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>لغو شده
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">از تاریخ</label>
                        <input type="text" name="start_date" class="form-control" data-jdp
                            value="{{ request('start_date') }}" placeholder="1403/01/01" autocomplete="off">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">تا تاریخ</label>
                        <input type="text" name="end_date" class="form-control" data-jdp
                            value="{{ request('end_date') }}" placeholder="1403/12/29" autocomplete="off">
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-filter me-1"></i>
                                فیلتر
                            </button>
                            <a href="{{ route('my-elections.index') }}" class="btn btn-secondary w-100">
                                <i class="ti ti-refresh me-1"></i>
                                حذف فیلتر
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
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

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="#home" data-bs-toggle="tab" aria-expanded="true" class="nav-link active" aria-selected="true"
                role="tab">
                انتخابات در حال برگزاری <span class="badge bg-danger">{{ $availableElections->count() }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a href="#profile" data-bs-toggle="tab" aria-expanded="false" class="nav-link" aria-selected="false"
                tabindex="-1" role="tab">
                انتخابات گذشته <span class="badge bg-success">{{ $unavailableElections->count() }}</span>
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
                                            <small class="text-muted">
                                                تاریخ ایجاد: {{ verta($election->created_at)->format('Y/m/d') }}
                                            </small>
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
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="me-1">
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
                <div class="row">
                    @foreach ($unavailableElections as $item)
                        @php
                            $election = $item['election'];
                            $event = $item['event'];
                            $group = $event->group;
                            $participant = $item['participant'];
                        @endphp
                        <div class="col-xl-4 col-lg-12 mb-2">
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
                                        <div class="d-flex align-items-center gap-2 flex-nowrap">
                                            <span
                                                class="badge badge-soft-info">{{ $item['election']->status->toFa() }}</span>
                                            @if ($election->isFinished())
                                                <form action="{{ route('my-elections.destroy', $election) }}"
                                                    method="POST" class="mb-0"
                                                    onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این انتخابات را از لیست خود حذف کنید؟')">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-danger text-nowrap">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div class="row g-3 mb-3">
                                        <div>
                                            @php
                                                $participantVotes = $participant->votes;
                                            @endphp
                                            <div class="d-flex justify-content-center flex-wrap gap-1">
                                                @foreach ($election->candidates as $candidate)
                                                    @php
                                                        $candidateUser = $candidate?->user;
                                                        $candidateVote = $participantVotes->where(
                                                            'candidate_id',
                                                            $candidate->id,
                                                        );
                                                    @endphp
                                                    @if ($candidateVote->count() > 0)
                                                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-center border border-success rounded p-1 bg-soft-success"
                                                            style="border-style: solid !important;">
                                                            <img src="{{ $candidateUser?->profile_image }}"
                                                                alt="image" class="img-fluid avatar-lg rounded">
                                                            <p class="fw-bold">
                                                                {{ $candidateUser?->full_name }}
                                                            </p>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-circle-check text-success fs-2"></i>
                                                                <div class="fw-bold">
                                                                    {{ $candidateVote->first()?->vote_count }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-center px-1 py-3 border rounded"
                                                            style="border-style: solid !important;">
                                                            <img src="{{ $candidateUser?->profile_image }}"
                                                                alt="image" class="img-fluid avatar-lg rounded">
                                                            <p>
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
