@extends('app.layouts.app')

@section('head-tag')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">
    <style>
        jdp-container {
            z-index: 1100 !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">انتخابات</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">داشبورد</a></li>

                <li class="breadcrumb-item"><a href="{{ route('elections.index', [$group, $event]) }}">انتخابات</a></li>

                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    @if ($errors->has('ends_at'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('ends_at') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                    <h4 class="header-title">لیست انتخابات</h4>
                    @php

                        $isSpecial = $group->type->value === \App\Enums\GroupType::SPECIAL->value;
                        $totalNormal = (int) $group->normal_stock_count;
                        $totalPrefered = (int) $group->prefered_stock_count;

                        $allocatedNormal = $group->users->sum(fn($u) => (int) $u->pivot->normal_stock_count);
                        $allocatedPrefered = $group->users->sum(fn($u) => (int) $u->pivot->prefered_stock_count);

                        $remainingNormal = $totalNormal - $allocatedNormal;
                        $remainingPrefered = $totalPrefered - $allocatedPrefered;

                        $allAllocated = $remainingNormal === 0 && $remainingPrefered === 0;
                        $canCreate = !$isSpecial || $allAllocated;
                    @endphp

                    <div>
                        @can(\App\Enums\Permission::CREATE_ELECTIONS->value)
                            @if ($canCreate)
                                <a href="{{ route('elections.create', [$group->slug, $event]) }}"
                                    class="btn btn-success bg-gradient">
                                    <i class="ti ti-plus me-1"></i>ایجاد همه‌پرسی
                                </a>
                            @else
                                <button class="btn btn-secondary bg-gradient" disabled>
                                    <i class="ti ti-lock me-1"></i>
                                    ایجاد همه‌پرسی (سهام کامل تخصیص نیافته)
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th class="ps-3" style="width: 50px;">
                                </th>
                                <th>عنوان</th>
                                <th>نوع همه پرسی</th>
                                <th>وضعیت</th>
                                <th>موضوع</th>
                                <th>زمان پایان</th>
                                <th class="text-end pe-3" style="width: 3.5rem;">عملیات</th>
                            </tr>
                        </thead><!-- end thead -->

                        <tbody>
                            @forelse ($elections as $election)
                                <tr>
                                    <td class="ps-3">
                                    </td>
                                    <td>
                                        @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                            <a href="{{ $election['operations']['show'] }}"
                                                class="text-dark fw-medium">{{ $election['title'] }}</a>
                                        @else
                                            <span class="text-dark fw-medium">{{ $election['title'] }}</span>
                                        @endcan
                                        @if (!empty($election['is_runoff']))
                                            <span class="badge bg-warning-subtle text-warning ms-1">دور دوم</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $election['fa_type'] }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusValue = is_object($election['status'] ?? null)
                                                ? ($election['status']->value ?? null)
                                                : ($election['status'] ?? null);
                                            $needsCandidates = $statusValue === 'created'
                                                || $election['status'] === \App\Enums\ElectionStatus::CREATED;
                                            $needsAttendance = $statusValue === 'participants_attendees'
                                                || $election['status'] === \App\Enums\ElectionStatus::PARTICIPANTS_ATTENDEES;
                                            $readyToStart = $statusValue === 'waiting_to_start'
                                                || $election['status'] === \App\Enums\ElectionStatus::WAITING_TO_START;
                                        @endphp
                                        <span
                                            class="badge {{ $needsCandidates ? 'bg-warning-subtle text-warning' : ($readyToStart ? 'bg-primary-subtle text-primary' : 'badge-soft-success') }}">
                                            {{ $election['fa_status'] }}
                                        </span>
                                        @if (!empty($election['is_expired']))
                                            <span class="badge bg-danger ms-1">منقضی شده</span>
                                        @endif
                                        @if ($needsCandidates)
                                            <div class="small text-warning mt-1">
                                                <i class="ti ti-alert-circle me-1"></i>
                                                ابتدا نامزدها را تعیین کنید؛ بعد امکان شروع فعال می‌شود.
                                            </div>
                                        @elseif ($needsAttendance)
                                            <div class="small text-info mt-1">
                                                <i class="ti ti-info-circle me-1"></i>
                                                حضور و غیاب را تکمیل کنید.
                                            </div>
                                        @elseif ($readyToStart)
                                            <div class="small text-primary mt-1">
                                                <i class="ti ti-player-play me-1"></i>
                                                آماده شروع است.
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $election['position'] }}</td>
                                    <td>
                                        @if (!empty($election['ends_at']))
                                            <small
                                                class="{{ !empty($election['is_expired']) ? 'text-danger' : 'text-muted' }}">
                                                <i class="ti ti-clock me-1"></i>{{ $election['ends_at'] }}
                                            </small>
                                            @if (empty($election['is_expired']))
                                                <div>
                                                    <small class="text-primary election-countdown"
                                                        data-end="{{ $election['ends_at_raw'] }}"
                                                        data-id="{{ $election['id'] }}">
                                                        زمان باقی‌مانده: <span class="value"></span>
                                                    </small>
                                                </div>
                                            @endif
                                        @else
                                            <small class="text-muted">—</small>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 align-middle">
                                        @php
                                            $isOngoing =
                                                ($election['status'] === \App\Enums\ElectionStatus::ONGOING ||
                                                    (is_object($election['status']) &&
                                                        $election['status']->value === 'ongoing') ||
                                                    (is_string($election['status']) &&
                                                        $election['status'] === 'ongoing')) &&
                                                empty($election['is_expired']);
                                            $isPublicJoint =
                                                isset($election['type']) &&
                                                $election['type'] === \App\Enums\ElectionType::PUBLIC_JOINT->value;
                                            $isEditLocked =
                                                $election['status'] === \App\Enums\ElectionStatus::ONGOING ||
                                                $election['status'] === \App\Enums\ElectionStatus::COMPLETED ||
                                                (is_object($election['status']) &&
                                                    in_array(
                                                        $election['status']->value,
                                                        ['ongoing', 'completed'],
                                                        true,
                                                    )) ||
                                                (is_string($election['status']) &&
                                                    in_array($election['status'], ['ongoing', 'completed'], true));
                                        @endphp
                                        <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                                            @isset($election['operations']['next_step'])
                                                @php
                                                    $nsBtn = $election['operations']['next_step'];
                                                    $nsAction = $nsBtn['action'] ?? '';
                                                @endphp
                                                @if ($nsAction === 'assign_candidates')
                                                    <a href="{{ $nsBtn['url'] }}"
                                                        class="btn btn-sm btn-warning text-dark fw-semibold"
                                                        title="{{ $nsBtn['hint'] ?? '' }}">
                                                        <i class="ti ti-users me-1"></i>تعیین نامزدها
                                                    </a>
                                                @elseif ($nsAction === 'attendance')
                                                    <a href="{{ $nsBtn['url'] }}"
                                                        class="btn btn-sm btn-info text-white fw-semibold"
                                                        title="{{ $nsBtn['hint'] ?? '' }}">
                                                        <i class="ti ti-clipboard-check me-1"></i>حضور و غیاب
                                                    </a>
                                                @elseif ($nsAction === 'start')
                                                    <button type="button"
                                                        class="btn btn-sm btn-primary fw-semibold election-start-btn"
                                                        data-bs-toggle="modal" data-bs-target="#startElectionModal"
                                                        data-start-url="{{ $nsBtn['url'] }}"
                                                        title="{{ $nsBtn['hint'] ?? '' }}">
                                                        <i class="ti ti-player-play me-1"></i>شروع
                                                    </button>
                                                @endif
                                            @endisset

                                            <div class="dropdown position-static">
                                            <button class="btn btn-sm btn-light btn-icon border shadow-none text-muted"
                                                type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                                                aria-expanded="false" title="عملیات">
                                                <i class="ti ti-dots-vertical fs-18"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1"
                                                style="min-width: 14rem;">
                                                @isset($election['operations']['next_step'])
                                                    @php
                                                        $ns = $election['operations']['next_step'];
                                                        $nsTitle = $ns['title'] ?? '';
                                                        $nsAction = $ns['action'] ?? '';
                                                        $isStart = $nsAction === 'start';
                                                    @endphp
                                                    @if ($isStart && $ns['method'] === 'POST')
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item d-flex align-items-center gap-2 election-start-btn fw-semibold text-primary"
                                                                data-bs-toggle="modal" data-bs-target="#startElectionModal"
                                                                data-start-url="{{ $ns['url'] }}">
                                                                <i class="ti ti-player-play"></i>
                                                                <span>{{ $nsTitle }}</span>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                    @elseif (($nsAction === 'end') && $ns['method'] === 'POST')
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item d-flex align-items-center gap-2 election-end-btn fw-semibold text-danger"
                                                                data-bs-toggle="modal" data-bs-target="#endElectionModal"
                                                                data-end-url="{{ $ns['url'] }}"
                                                                data-election-title="{{ e($election['title']) }}">
                                                                <i class="ti ti-flag"></i>
                                                                <span>{{ $nsTitle }}</span>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                    @elseif ($ns['method'] === 'POST')
                                                        <li>
                                                            <form action="{{ $ns['url'] }}" method="POST" class="m-0">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold">
                                                                    <i class="ti ti-check"></i>
                                                                    <span>{{ $nsTitle }}</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-start gap-2 bg-warning-subtle"
                                                                href="{{ $ns['url'] }}">
                                                                <i class="ti ti-arrow-left text-warning mt-1"></i>
                                                                <span>
                                                                    <span class="d-block fw-semibold text-dark">{{ $nsTitle }}</span>
                                                                    @if (!empty($ns['hint']))
                                                                        <small class="text-muted d-block text-wrap" style="max-width: 12rem;">{{ $ns['hint'] }}</small>
                                                                    @endif
                                                                </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                    @endif
                                                @endisset

                                                @if ($needsCandidates)
                                                    <li>
                                                        <span
                                                            class="dropdown-item d-flex align-items-center gap-2 disabled text-muted mb-0">
                                                            <i class="ti ti-player-play"></i>
                                                            <span>شروع انتخابات (بعد از تعیین نامزد)</span>
                                                        </span>
                                                    </li>
                                                @endif

                                                @if ($isOngoing && !$isPublicJoint)
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                            href="{{ route('voting.create', [$group, $event, $election['slug']]) }}">
                                                            <i class="ti ti-vote text-info"></i>
                                                            <span>رای‌گیری</span>
                                                        </a>
                                                    </li>
                                                @elseif ($isOngoing && $isPublicJoint)
                                                    <li>
                                                        <span
                                                            class="dropdown-item d-flex align-items-center gap-2 disabled text-muted mb-0">
                                                            <i class="ti ti-vote"></i>
                                                            <span>رای‌گیری (تعاونی از داشبورد)</span>
                                                        </span>
                                                    </li>
                                                @else
                                                    <li>
                                                        <span
                                                            class="dropdown-item d-flex align-items-center gap-2 disabled text-muted mb-0">
                                                            <i class="ti ti-vote"></i>
                                                            <span>{{ !empty($election['is_expired']) ? 'رای‌گیری (منقضی)' : 'رای‌گیری (شروع نشده)' }}</span>
                                                        </span>
                                                    </li>
                                                @endif

                                                @if (isset($election['has_votes']) && $election['has_votes'])
                                                    @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                                href="{{ $election['report_url'] ?? route('elections.report', [$group, $event, $election['slug']]) }}">
                                                                <i class="ti ti-file-text text-warning"></i>
                                                                <span>گزارش انتخابات</span>
                                                            </a>
                                                        </li>
                                                    @endcan
                                                @endif

                                                @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                            href="{{ $election['operations']['show'] }}">
                                                            <i class="ti ti-eye text-success"></i>
                                                            <span>مشاهده جزئیات</span>
                                                        </a>
                                                    </li>
                                                @endcan

                                                @can(\App\Enums\Permission::EDIT_ELECTIONS->value)
                                                    @if ($isEditLocked)
                                                        <li>
                                                            <span
                                                                class="dropdown-item d-flex align-items-center gap-2 disabled text-muted mb-0">
                                                                <i class="ti ti-edit"></i>
                                                                <span>ویرایش انتخابات (شروع شده)</span>
                                                            </span>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                                href="{{ $election['operations']['edit'] }}">
                                                                <i class="ti ti-edit text-secondary"></i>
                                                                <span>ویرایش انتخابات</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endcan

                                                @canany([\App\Enums\Permission::EDIT_CANDIDATES->value, \App\Enums\Permission::CREATE_CANDIDATES->value])
                                                    @if ($isEditLocked)
                                                        <li>
                                                            <span
                                                                class="dropdown-item d-flex align-items-center gap-2 disabled text-muted mb-0">
                                                                <i class="ti ti-users"></i>
                                                                <span>ویرایش تعیین نامزد (شروع شده)</span>
                                                            </span>
                                                        </li>
                                                    @elseif (!$needsCandidates)
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                                href="{{ $election['operations']['edit_candidates'] }}">
                                                                <i class="ti ti-users text-secondary"></i>
                                                                <span>ویرایش تعیین نامزد</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endcanany

                                                @can(\App\Enums\Permission::CREATE_ELECTIONS->value)
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item d-flex align-items-center gap-2"
                                                            data-election-template-b64="{{ base64_encode($election['template_block']) }}"
                                                            onclick="copyElectionTemplateFromButton(this)">
                                                            <i class="ti ti-copy text-secondary"></i>
                                                            <span>کپی قالب مشخصات</span>
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                            href="{{ $election['operations']['create_duplicate'] }}">
                                                            <i class="ti ti-copy-plus text-primary"></i>
                                                            <span>ایجاد مشابه</span>
                                                        </a>
                                                    </li>
                                                    @if ($otherEvents->isNotEmpty())
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item d-flex align-items-center gap-2"
                                                                onclick="openCopyToEventModal({{ $election['id'] }}, '{{ e($election['title']) }}')">
                                                                <i class="ti ti-calendar-share text-info"></i>
                                                                <span>کپی به رویداد دیگر</span>
                                                            </button>
                                                        </li>
                                                    @endif
                                                @endcan

                                                @can(\App\Enums\Permission::DELETE_ELECTIONS->value)
                                                    @if (!empty($election['can_delete']))
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                        <li>
                                                            <form action="{{ $election['operations']['delete'] }}"
                                                                method="post" class="m-0"
                                                                onsubmit="return confirm('این انتخابات حذف شود؟ این عمل قابل بازگشت نیست.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                                    <i class="ti ti-trash"></i>
                                                                    <span>حذف انتخابات</span>
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                @endcan
                                            </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted">هنوز هیچ انتخاباتی برگزار نشده است.</td>
                                </tr>
                            @endforelse

                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="copyToEventModal" tabindex="-1" aria-labelledby="copyToEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="copy-to-event-form" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="copyToEventModalLabel">
                            <i class="ti ti-calendar-share me-2 text-info"></i>کپی انتخابات به رویداد دیگر
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            انتخابات <strong id="copy-election-title-display"></strong> با تمام کاندیداهایش در رویداد مقصد
                            کپی می‌شود و آماده شروع خواهد بود.
                        </p>
                        <div class="mb-3">
                            <label for="copy-target-event-select" class="form-label fw-semibold">رویداد مقصد</label>
                            <select class="form-select" id="copy-target-event-select">
                                @foreach ($otherEvents as $oe)
                                    <option value="{{ $oe->slug }}">{{ $oe->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-info text-white" id="confirm-copy-to-event-btn">
                            <i class="ti ti-calendar-share me-1"></i>کپی کن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="startElectionModal" tabindex="-1" aria-labelledby="startElectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="startElectionForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="startElectionModalLabel">شروع انتخابات</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">زمان شروع به‌صورت خودکار همین لحظه ثبت می‌شود.</p>
                        <div class="mb-3">
                            <label for="start_election_ends_at" class="form-label">زمان پایان انتخابات <span
                                    class="text-muted fw-normal">(اختیاری)</span></label>
                            <input type="text" class="form-control @error('ends_at') is-invalid @enderror"
                                name="ends_at" id="start_election_ends_at" autocomplete="off" data-jdp
                                data-jdp-time-picker="true" placeholder="۱۴۰۳/۰۱/۰۱ ۱۴:۳۰"
                                value="{{ old('ends_at') }}">
                            @error('ends_at')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">در صورت خالی ماندن، محدودیت زمانی خودکار برای پایان
                                اعمال نمی‌شود.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-primary">شروع انتخابات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="endElectionModal" tabindex="-1" aria-labelledby="endElectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="endElectionForm" method="POST" action="">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-danger" id="endElectionModalLabel">
                            <i class="ti ti-flag me-1"></i>پایان انتخابات
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning mb-3">
                            <i class="ti ti-alert-triangle me-1"></i>
                            این عمل قابل بازگشت نیست.
                        </div>
                        <p class="mb-2">
                            آیا از پایان انتخابات
                            <strong id="end-election-title-display"></strong>
                            مطمئن هستید؟
                        </p>
                        <p class="text-muted small mb-0">
                            پس از تأیید، وضعیت انتخابات به «پایان یافته» تغییر می‌کند و امکان رأی‌گیری جدید وجود نخواهد داشت.
                        </p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-flag me-1"></i>بله، پایان بده
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/samples/assets/irregular-data-series.js"></script>

    <script src="/assets/js/pages/chart-apex-bar.js"></script>
    <script>
        var copyToEventModal = null;
        var copyElectionId = null;
        var copyBaseUrl = "{{ url($group->slug . '/events') }}";

        function openCopyToEventModal(id, title) {
            copyElectionId = id;
            document.getElementById('copy-election-title-display').textContent = title;
            if (!copyToEventModal) {
                copyToEventModal = new bootstrap.Modal(document.getElementById('copyToEventModal'));
            }
            updateCopyFormAction();
            copyToEventModal.show();
        }

        function updateCopyFormAction() {
            var targetSlug = document.getElementById('copy-target-event-select').value;
            if (!targetSlug || !copyElectionId) return;
            document.getElementById('copy-to-event-form').action =
                copyBaseUrl + '/' + targetSlug + '/elections/copy-from/' + copyElectionId;
        }

        document.getElementById('copy-target-event-select').addEventListener('change', updateCopyFormAction);

        document.getElementById('confirm-copy-to-event-btn').addEventListener('click', updateCopyFormAction);

        document.querySelectorAll('.election-start-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-start-url');
                var form = document.getElementById('startElectionForm');
                if (form && url) {
                    form.setAttribute('action', url);
                }
            });
        });

        document.querySelectorAll('.election-end-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var url = this.getAttribute('data-end-url');
                var title = this.getAttribute('data-election-title') || '';
                var form = document.getElementById('endElectionForm');
                var titleEl = document.getElementById('end-election-title-display');
                if (form && url) {
                    form.setAttribute('action', url);
                }
                if (titleEl) {
                    titleEl.textContent = title;
                }
            });
        });

        if (typeof jalaliDatepicker !== 'undefined') {
            jalaliDatepicker.startWatch();
        }

        function copyElectionTemplateFromButton(btn) {
            var b64 = btn.getAttribute('data-election-template-b64');
            if (!b64) return;
            var bin = atob(b64);
            var bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) {
                bytes[i] = bin.charCodeAt(i);
            }
            var text = new TextDecoder('utf-8').decode(bytes);
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    if (typeof Toast !== 'undefined') {
                        Toast.create({
                            title: 'کپی شد',
                            message: 'متن قالب در کلیپ‌بورد قرار گرفت.',
                            type: 'success',
                            duration: 2500
                        });
                    } else {
                        alert('متن قالب کپی شد.');
                    }
                }).catch(function() {
                    window.prompt('کپی دستی:', text);
                });
            } else {
                window.prompt('کپی دستی:', text);
            }
        }

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
