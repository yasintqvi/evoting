<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش انتخابات - {{ $election->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #222;
            font-size: 12px;
        }

        h2, h3, p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 16px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }

        .meta {
            margin-top: 6px;
            color: #666;
            font-size: 11px;
        }

        .stats {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
        }

        .stats td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            width: 25%;
        }

        .stats strong {
            display: block;
            font-size: 15px;
            margin-bottom: 3px;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.results th,
        table.results td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: middle;
        }

        table.results th {
            background: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #666;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $mainWinnersCount = (int) ($election->main_member_count ?? 0);
        $substituteWinnersCount = (int) ($election->substitute_member_count ?? 0);
        $winnersLimit = $mainWinnersCount + $substituteWinnersCount;
        $elected = $candidateVotes->take($winnersLimit > 0 ? $winnersLimit : $candidateVotes->count());
    @endphp

    <div class="header">
        <h2>گزارش نتایج انتخابات</h2>
        <p style="margin-top:6px;">{{ $election->title }}</p>
        <div class="meta">
            گروه: {{ $group->title ?? '-' }}
            |
            رویداد: {{ $event->title ?? '-' }}
            |
            تاریخ:
            {{ function_exists('verta') ? verta(now('Asia/Tehran'))->format('Y/m/d H:i') : now('Asia/Tehran')->format('Y-m-d H:i') }}
        </div>
    </div>

    <table class="stats">
        <tr>
            <td>
                <strong>{{ $totalCandidates }}</strong>
                <span class="muted">تعداد کاندیدا</span>
            </td>
            <td>
                <strong>{{ $totalParticipants }}</strong>
                <span class="muted">مشارکت‌کنندگان</span>
            </td>
            <td>
                <strong>{{ number_format($totalVotes) }}</strong>
                <span class="muted">آرای ثبت‌شده</span>
            </td>
            <td>
                <strong>{{ $elected->count() }}</strong>
                <span class="muted">منتخبان</span>
            </td>
        </tr>
    </table>

    <h3 style="margin-bottom:8px;">افراد انتخاب‌شده</h3>

    @if ($elected->count() > 0)
        <table class="results">
            <thead>
                <tr>
                    <th class="text-center" style="width:60px;">رتبه</th>
                    <th>نام</th>
                    <th class="text-center" style="width:120px;">تعداد آرا</th>
                    <th class="text-center" style="width:120px;">وضعیت</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($elected as $index => $candidate)
                    @php
                        $voteCount = (int) ($candidate->votes_sum_vote_count ?? 0);
                        $status = $index < $mainWinnersCount ? 'عضو اصلی' : 'علی‌البدل';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $candidate->user?->full_name ?? '—' }}</td>
                        <td class="text-center">{{ number_format($voteCount) }}</td>
                        <td class="text-center">{{ $status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">هنوز فردی انتخاب نشده است.</p>
    @endif

    <div class="footer">
        این گزارش به‌صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
