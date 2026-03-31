@extends('app.layouts.app')

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
                                <th class="text-center" style="width: 120px;">اقدامات</th>
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
                                    </td>
                                    <td>
                                        <small>{{ $election['fa_type'] }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-soft-success">{{ $election['fa_status'] }}</span>
                                        @if (!empty($election['is_expired']))
                                            <span class="badge bg-danger ms-1">منقضی شده</span>
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
                                    <td class="pe-3">
                                        <div class="hstack gap-1 justify-content-end">
                                            {{-- @isset($election['operations']['next_step'])
                                                <a href="{{ $election['operations']['next_step']['url'] }}"
                                                    class="btn btn-primary btn-sm">
                                                    {{ $election['operations']['next_step']['title'] }}
                                                </a>
                                            @endisset --}}
                                            @isset($election['operations']['next_step'])
                                                @php
                                                    $isFinish =
                                                        $election['operations']['next_step']['title'] ===
                                                        'پایان انتخابات';
                                                    $btnClass = $isFinish
                                                        ? 'btn btn-danger btn-sm'
                                                        : 'btn btn-primary btn-sm';
                                                @endphp

                                                @if ($election['operations']['next_step']['method'] === 'POST')
                                                    <form action="{{ $election['operations']['next_step']['url'] }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="{{ $btnClass }}">
                                                            {{ $election['operations']['next_step']['title'] }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ $election['operations']['next_step']['url'] }}"
                                                        class="{{ $btnClass }}">
                                                        {{ $election['operations']['next_step']['title'] }}
                                                    </a>
                                                @endif
                                            @endisset
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
                                            @endphp
                                            @if ($isOngoing && !$isPublicJoint)
                                                <a href="{{ route('voting.create', [$group, $event, $election['slug']]) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="ti ti-vote me-1"></i>رای گیری
                                                </a>
                                            @elseif ($isOngoing && $isPublicJoint)
                                                <button class="btn btn-info btn-sm" disabled
                                                    title="رای گیری برای انتخابات تعاونی از طریق داشبورد انجام می‌شود">
                                                    <i class="ti ti-vote me-1"></i>رای گیری
                                                </button>
                                            @else
                                                <button class="btn btn-info btn-sm" disabled
                                                    title="{{ !empty($election['is_expired']) ? 'انتخابات منقضی شده است' : 'انتخابات هنوز شروع نشده است' }}">
                                                    <i class="ti ti-vote me-1"></i>رای گیری
                                                </button>
                                            @endif
                                            @if (isset($election['has_votes']) && $election['has_votes'])
                                                @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                                    <a href="{{ $election['report_url'] ?? route('elections.report', [$group, $event, $election['slug']]) }}"
                                                        class="btn btn-warning btn-sm" title="گزارش انتخابات">
                                                        <i class="ti ti-file-text me-1"></i>گزارش
                                                    </a>
                                                @endcan
                                            @endif
                                            @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                                <a href="{{ $election['operations']['show'] }}"
                                                    class="btn btn-success btn-sm"><i class="ti ti-eye"></i></a>
                                            @endcan
                                            @can(\App\Enums\Permission::EDIT_ELECTIONS->value)
                                                @php
                                                    // بررسی وضعیت‌های غیرقابل ویرایش: در حال اجرا یا تکمیل شده
                                                    $isLocked =
                                                        $election['status'] === \App\Enums\ElectionStatus::ONGOING ||
                                                        $election['status'] === \App\Enums\ElectionStatus::COMPLETED ||
                                                        (is_object($election['status']) &&
                                                            in_array($election['status']->value, [
                                                                'ongoing',
                                                                'completed',
                                                            ])) ||
                                                        (is_string($election['status']) &&
                                                            in_array($election['status'], ['ongoing', 'completed']));
                                                @endphp

                                                @if ($isLocked)
                                                    <button class="btn btn-secondary btn-sm" disabled
                                                        title="ویرایش این انتخابات مجاز نیست">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                @else
                                                    <a href="{{ $election['operations']['edit'] }}"
                                                        class="btn btn-secondary btn-sm">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                            {{-- @can(\App\Enums\Permission::DELETE_ELECTIONS->value)
                                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm"><i
                                                            class="ti ti-trash"></i></a>
                                                @endcan --}}
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
@endsection

@section('scripts')
    <script src="/assets/samples/assets/irregular-data-series.js"></script>
    
    <script src="/assets/js/pages/chart-apex-bar.js"></script>
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
