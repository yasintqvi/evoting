<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش تفصیلی آرا - {{ $election->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #222;
            font-size: 12px;
            margin: 20px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 16px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }

        .meta {
            margin-top: 6px;
            color: #666;
            font-size: 11px;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.results th,
        table.results td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: middle;
        }

        table.results th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #666;
        }

        tr.subtotal td {
            background: #fafafa;
            font-weight: bold;
        }

        tr.grand-total td {
            background: #eee;
            font-weight: bold;
        }

        .page-break {
            page-break-before: always;
            break-before: page;
        }

        table.results thead {
            display: table-header-group;
        }

        table.results tr {
            page-break-inside: avoid;
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        @media print {
            body {
                margin: 0;
            }
        }

        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    @php
        $rowsPerPage = 25;
        $pages = $voterRows->chunk($rowsPerPage);
        $grandTotals = $candidates->mapWithKeys(fn ($candidate) => [
            $candidate->id => $voterRows->sum(fn ($row) => (float) ($row['votes_by_candidate'][$candidate->id] ?? 0)),
        ]);
        $fmtDate = fn ($d) => $d ? verta($d->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') : '—';
    @endphp

    <div class="header" style="text-align:center;">
        <p style="margin-top:18px;">{{ $group->title ?? '-' }}</p>
        <p style="margin-top:6px; font-size:14px; color:#666;">{{ $event->name ?? '-' }}</p>
        <h2 style="font-size:11px; color:#666;">گزارش تفصیلی آرای ثبت شده {{ $election->title }}</h2>
        <div class="meta">
            <span>تاریخ گزارش: {{ verta(now('Asia/Tehran'))->format('Y/m/d H:i') }}</span>
        </div>
    </div>

    <h3 style="margin-bottom:8px;">لیست رای‌دهندگان و تعداد رای به هر کاندیدا</h3>
    @if ($voterRows->count() > 0)
        @foreach ($pages as $pageIndex => $pageRows)
            <table class="results {{ $pageIndex > 0 ? 'page-break' : '' }}">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">ردیف</th>
                        <th class="text-center">کد ملی</th>
                        @foreach ($candidates as $candidate)
                            <th class="text-center">{{ $candidate->user?->full_name ?? '---' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pageRows as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center" dir="ltr">{{ $row['masked_national_code'] }}</td>
                            @foreach ($candidates as $candidate)
                                <td class="text-center" dir="ltr">
                                    {{ isset($row['votes_by_candidate'][$candidate->id]) ? number_format($row['votes_by_candidate'][$candidate->id], 2) : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    @if ($pages->count() > 1)
                        <tr class="subtotal">
                            <td class="text-center" colspan="2">جمع صفحه {{ $pageIndex + 1 }}</td>
                            @foreach ($candidates as $candidate)
                                <td class="text-center" dir="ltr">
                                    {{ number_format($pageRows->sum(fn ($row) => (float) ($row['votes_by_candidate'][$candidate->id] ?? 0)), 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endif
                    @if ($loop->last)
                        <tr class="grand-total">
                            <td class="text-center" colspan="2">جمع کل</td>
                            @foreach ($candidates as $candidate)
                                <td class="text-center" dir="ltr">{{ number_format($grandTotals[$candidate->id], 2) }}</td>
                            @endforeach
                        </tr>
                    @endif
                </tbody>
            </table>
        @endforeach
    @else
        <p class="muted">هنوز رای‌ای برای این انتخابات ثبت نشده است.</p>
    @endif

    <div class="footer">
        <div style="margin-bottom:4px;">
            تاریخ شروع انتخابات: {{ $fmtDate($election->starts_at) }}
            |
            تاریخ پایان انتخابات: {{ $fmtDate($election->ends_at) }}
        </div>
        این گزارش به صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
