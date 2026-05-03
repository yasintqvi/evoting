@extends('app.layouts.app')

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
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge badge-soft-info">{{ $item['election']->status->toFa() }}</span>
                                            <form action="{{ route('my-elections.destroy', $election->slug) }}"
                                                method="POST"
                                                onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این انتخابات را از لیست خود حذف کنید؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    حذف
                                                </button>
                                            </form>
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
                                                        $user = $candidate?->user;
                                                        $candidateVote = $participantVotes->where('candidate_id', $candidate->id);
                                                    @endphp
                                                    @if ($candidateVote->count() > 0)
                                                        <div class="col-md-3 d-flex flex-column justify-content-center align-items-center border border-success rounded p-1 bg-soft-success"
                                                            style="border-style: solid !important;">
                                                            <img src="{{ $user?->profile_image }}" alt="image"
                                                                class="img-fluid avatar-lg rounded">
                                                            <p class="fw-bold">
                                                                {{ $user?->full_name }}
                                                            </p>
                                                            <div class="d-flex align-items-center gap-1">
                                                                <i class="ti ti-circle-check text-success fs-2"></i>
                                                                <div class="fw-bold">{{ $candidateVote->first()?->vote_count }}</div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="col-md-3 d-flex flex-column justify-content-center align-items-center px-1 py-3 border rounded" style="border-style: solid !important;">
                                                            <img src="{{ $user?->profile_image }}" alt="image"
                                                                class="img-fluid avatar-lg rounded">
                                                            <p>
                                                                {{ $user?->full_name }}
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
