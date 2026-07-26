@extends('app.layouts.app')
@section('head-tag')
    <style>
        * {
            /* font-family: 'DejaVu Sans', sans-serif !important; */
        }

        body {
            direction: rtl;
        }

        .card-title {
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">خانه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('elections.index', [$group, $event]) }}">انتخابات</a></li>
                <li class="breadcrumb-item active">گزارش</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        آمار کلی انتخابات
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border border-dashed bg-light">
                                <div class="card-body text-center p-4">
                                    <h3 class="mb-1 text-primary fw-bold">{{ $totalCandidates }}</h3>
                                    <p class="mb-0 text-muted">تعداد کاندیدا</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border border-dashed bg-light">
                                <div class="card-body text-center p-4">
                                    <h3 class="mb-1 text-success fw-bold">{{ $totalParticipants }}</h3>
                                    <p class="mb-0 text-muted">تعداد مشارکت کنندگان</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border border-dashed bg-light">
                                <div class="card-body text-center p-4">
                                    <h3 class="mb-1 text-info fw-bold">{{ number_format($totalVotes) }}</h3>
                                    <p class="mb-0 text-muted">تعداد آرای ثبت شده</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border border-dashed bg-light">
                                <div class="card-body text-center p-4">
                                    <h3 class="mb-1 text-secondary fw-bold">
                                        {{ number_format($election->all_votes) }}
                                    </h3>
                                    <p class="mb-0 text-muted">کل آرای قابل ثبت (سقف)</p>
                                    @if ($election->type === App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                        <small class="text-muted d-block mt-1">مجموع سهام حاضرین × تعداد اعضای اصلی</small>
                                    @elseif ($election->type === App\Enums\ElectionType::PRIVATE_JOINT)
                                        <small class="text-muted d-block mt-1">مجموع سهام حاضرین × حداکثر انتخاب مجاز</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-block">
                            <h4 class="card-title mb-0">نمودار نتایج</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="candidate-chart" class="apex-charts" data-colors="#39afd1,#ffbc00"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            @if (!empty($tieBreak['has_tie']) && ! $election->isRunoff())
                <div class="alert alert-warning border-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
                    <div>
                        <h5 class="alert-heading mb-1">
                            <i class="ti ti-scale me-1"></i>تساوی در مرز اصلی و علی‌البدل
                        </h5>
                        <p class="mb-1">{{ $tieBreak['message'] }}</p>
                        <ul class="mb-0 small">
                            @foreach ($tieBreak['candidates'] as $tiedCandidate)
                                <li>
                                    {{ $tiedCandidate->user?->full_name }}
                                    —
                                    {{ number_format((int) ($tiedCandidate->votes_sum_vote_count ?? 0)) }} رأی
                                </li>
                            @endforeach
                        </ul>
                        <p class="mb-0 mt-2 small text-muted">
                            کرسی‌های مورد اختلاف:
                            {{ (int) $tieBreak['contested_main_seats'] }} اصلی
                            @if ((int) $tieBreak['contested_substitute_seats'] > 0)
                                و {{ (int) $tieBreak['contested_substitute_seats'] }} علی‌البدل
                            @endif
                        </p>
                    </div>
                    <div class="text-md-end">
                        @if ($election->status !== App\Enums\ElectionStatus::COMPLETED)
                            <span class="badge bg-secondary">پس از پایان انتخابات می‌توانید دور دوم را شروع کنید</span>
                        @elseif ($existingRunoff)
                            <a href="{{ route('elections.report', [$group, $event, $existingRunoff]) }}"
                                class="btn btn-outline-primary mb-2 d-block">
                                مشاهده دور دوم
                            </a>
                            @if ($existingRunoff->status === App\Enums\ElectionStatus::WAITING_TO_START)
                                <form action="{{ route('elections.start', [$group, $event, $existingRunoff]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning d-block w-100">شروع رأی‌گیری دور دوم</button>
                                </form>
                            @elseif ($existingRunoff->status === App\Enums\ElectionStatus::ONGOING)
                                <span class="badge bg-primary">دور دوم در حال برگزاری است</span>
                            @elseif ($existingRunoff->status === App\Enums\ElectionStatus::COMPLETED)
                                <span class="badge bg-success">دور دوم پایان یافته</span>
                            @endif
                        @else
                            @can(\App\Enums\Permission::CREATE_ELECTION_ROUNDS->value)
                                <form action="{{ route('elections.start-runoff', [$group, $event, $election]) }}"
                                    method="POST"
                                    onsubmit="return confirm('انتخابات دور دوم فقط با کاندیداهای هم‌رأی ساخته می‌شود. ادامه می‌دهید؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning fw-semibold">
                                        <i class="ti ti-repeat me-1"></i>شروع دور دوم
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>
                </div>
            @endif

            @if ($election->isRunoff() && $election->parentElection)
                <div class="alert alert-info mb-3">
                    این گزارش مربوط به
                    <strong>دور دوم</strong>
                    برای شکستن تساوی انتخابات
                    «{{ $election->parentElection->title }}»
                    است.
                    <a href="{{ route('elections.report', [$group, $event, $election->parentElection]) }}" class="alert-link">
                        بازگشت به گزارش انتخابات اصلی
                    </a>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        جدول نتایج
                    </h4>
                </div>
                <div class="card-body">
                    @php
                        $mainWinnersCount = (int) ($election->main_member_count ?? 0);
                        $substituteWinnersCount = (int) ($election->substitute_member_count ?? 0);
                    @endphp

                    @if ($candidateVotes->count() > 0)
                        @if ($totalVotes == 0)
                            <div class="alert alert-warning">
                                هنوز رای‌ای برای این انتخابات ثبت نشده است؛ اما لیست نامزدها نمایش داده می‌شود.
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-nowrap mb-0">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">رتبه</th>
                                        <th>نام کاندید</th>
                                        <th class="text-center">تعداد آرا</th>
                                        <th class="text-center" style="width: 240px;"><small>درصد آرا (نسبت به آرای اختصاص
                                                داده شده)</small></th>
                                        <th class="text-center" style="width: 240px;">درصد آرا (نسبت به کل آرا)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($candidateVotes as $index => $candidate)
                                        @php
                                            $candidateType =
                                                $index < $mainWinnersCount
                                                    ? 'main'
                                                    : ($index < $mainWinnersCount + $substituteWinnersCount
                                                        ? 'substitute'
                                                        : 'invalid');
                                            $badgeClass =
                                                $candidateType === 'main'
                                                    ? 'badge-soft-success'
                                                    : ($candidateType === 'substitute'
                                                        ? 'badge-soft-warning'
                                                        : 'badge-soft-danger');
                                            $barClass =
                                                $candidateType === 'main'
                                                    ? 'bg-success'
                                                    : ($candidateType === 'substitute'
                                                        ? 'bg-warning'
                                                        : 'bg-danger');

                                            $voteCount = (int) ($candidate->votes_sum_vote_count ?? 0);
                                            $percentage = $totalVotes > 0 ? ($voteCount / $totalVotes) * 100 : 0;
                                            $percentageByAllVotes =
                                                $election->all_votes > 0
                                                    ? ($voteCount / $election->all_votes) * 100
                                                    : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge {{ $badgeClass }} fs-16 fw-semibold">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ asset($candidate->user->profile_image) }}"
                                                        alt="{{ $candidate->user->full_name }}"
                                                        class="avatar-sm rounded-circle">
                                                    <div>
                                                        <h6 class="mb-0 fw-semibold">
                                                            {{ $candidate->user->full_name }}
                                                        </h6>
                                                        <small
                                                            class="text-muted">{{ $candidate->user->national_code ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <h5 class="mb-0 fw-bold text-primary">
                                                    {{ number_format($voteCount) }}
                                                </h5>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;position: relative;">
                                                    <div class="progress-bar {{ $barClass }}" role="progressbar"
                                                        style="width: {{ $percentage }}%;"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <span
                                                            class="text-dark fw-semibold"style="position:absolute; left: 2%">{{ number_format($percentage, 1) }}%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 25px;position: relative;">
                                                    <div class="progress-bar {{ $barClass }}" role="progressbar"
                                                        style="width: {{ $percentageByAllVotes }}%"
                                                        aria-valuenow="{{ $percentageByAllVotes }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        <span class="text-dark fw-semibold"
                                                            style="position:absolute; left: 2%">{{ number_format($percentageByAllVotes, 1) }}%</span>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-muted mb-3">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <p class="text-muted fs-16">هنوز نامزدی برای این انتخابات ثبت نشده است.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0 d-flex align-items-center gap-2">
                        دریافت خروجی
                    </h4>
                </div>
                <div class="card-body">
                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('elections.report', [$group, $event, $election]) }}?download_pdf=1"
                                class="btn btn-danger" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                دانلود گزارش PDF
                            </a>

                            <a href="{{ route('elections.detailed-report', [$group, $event, $election]) }}?download_pdf=1"
                                class="btn btn-danger" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                دانلود گزارش PDF با جزئیات
                            </a>

                            <a href="{{ route('elections.report', [$group, $event, $election]) }}?download_excel=1"
                                class="btn btn-success" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16v16H4z"></path>
                                    <path d="M10 12l-2-2l-2 2l2 2z"></path>
                                    <path d="M14 12l2-2l2 2l-2 2z"></path>
                                </svg>
                                دانلود گزارش Excel
                            </a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $chartData = [];
        $chartLabels = [];
        $chartColors = [];

        $totalVotesForCandidates = 0;
        foreach ($candidateVotes as $candidate) {
            $totalVotesForCandidates += $candidate->votes_sum_vote_count ?? 0;
        }

        $unassigned_votes_count = $election->all_votes - $totalVotes;
        $totalVotesAll = $totalVotesForCandidates + $unassigned_votes_count;

        if ($totalVotesAll == 0) {
            $totalVotesAll = 1;
        }

        $mainWinnersCount = (int) ($election->main_member_count ?? 0);
        $substituteWinnersCount = (int) ($election->substitute_member_count ?? 0);

        foreach ($candidateVotes as $index => $candidate) {
            $candidateType =
                $index < $mainWinnersCount
                    ? 'main'
                    : ($index < $mainWinnersCount + $substituteWinnersCount
                        ? 'substitute'
                        : 'invalid');
            $voteCount = (int) ($candidate->votes_sum_vote_count ?? 0);
            $percentage = ($voteCount / $totalVotesAll) * 100;

            $chartData[] = round($percentage, 2);
            $chartLabels[] = $candidate->user->full_name;
            $chartColors[] =
                $candidateType === 'main' ? '#198754' : ($candidateType === 'substitute' ? '#fd7e14' : '#dc3545');
        }

        $unassignedPercentage = ($unassigned_votes_count / $totalVotesAll) * 100;
        $chartData[] = round($unassignedPercentage, 2);
        $chartLabels[] = 'آرای اخذ نشده';
        $chartColors[] = '#6c757d';

    @endphp
@endsection


@section('scripts')
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var options = {
                chart: {
                    type: 'pie',
                    height: 400
                },
                series: @json($chartData),
                labels: @json($chartLabels),
                colors: @json($chartColors),
                legend: {
                    position: 'bottom',
                    fontSize: '14px'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val.toLocaleString() + " درصد آرا";
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#candidate-chart"), options);
            chart.render();
        });
    </script>
@endsection
