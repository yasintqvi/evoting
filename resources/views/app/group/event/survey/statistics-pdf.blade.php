<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش نتایج نظرسنجی — {{ $survey->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #1f2937;
            font-size: 10.5px;
        }

        h1, h2, h3, p { margin: 0; padding: 0; }

        .brand {
            width: 100%;
            border-collapse: collapse;
            background: #1e3a8a;
            color: #fff;
        }

        .brand td { padding: 14px 18px; vertical-align: middle; }
        .brand .org { font-size: 15px; font-weight: bold; }
        .brand .doc { font-size: 10px; color: #c7d2fe; margin-top: 3px; }
        .brand .date { text-align: left; font-size: 9.5px; color: #e0e7ff; }

        .title-box {
            margin-top: 12px;
            padding: 10px 14px;
            border-right: 4px solid #2563eb;
            background: #f1f5f9;
        }

        .title-box .t { font-size: 14px; font-weight: bold; color: #0f172a; }
        .title-box .s { font-size: 10px; color: #475569; margin-top: 4px; }

        table.kpis { width: 100%; border-collapse: separate; border-spacing: 6px 0; margin: 12px -6px 4px; }
        table.kpis td {
            width: 25%;
            border: 1px solid #e2e8f0;
            background: #fff;
            text-align: center;
            padding: 9px 4px;
        }
        .kv { font-size: 18px; font-weight: bold; color: #1e3a8a; }
        .kl { font-size: 9px; color: #64748b; margin-top: 3px; }

        .note {
            margin-top: 10px;
            padding: 7px 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 9.5px;
        }

        .q {
            margin-top: 16px;
            border: 1px solid #e2e8f0;
            page-break-inside: avoid;
        }

        .q-head {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .q-head td { padding: 8px 12px; vertical-align: middle; }
        .q-num {
            width: 26px;
            text-align: center;
            background: #2563eb;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }
        .q-title { font-size: 11.5px; font-weight: bold; color: #0f172a; }
        .q-meta { text-align: left; font-size: 9px; color: #64748b; white-space: nowrap; }

        table.opts { width: 100%; border-collapse: collapse; }
        table.opts th {
            background: #fff;
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            padding: 6px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.opts td {
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        table.opts tr.winner td { background: #eff6ff; }
        table.opts tr.total td {
            background: #f8fafc;
            font-weight: bold;
            border-top: 1px solid #e2e8f0;
            border-bottom: none;
        }

        .c { text-align: center; }
        .num { font-weight: bold; color: #0f172a; }
        .pct { font-weight: bold; color: #1d4ed8; }
        .badge {
            font-size: 8px;
            color: #fff;
            background: #16a34a;
            padding: 1px 5px;
        }


        table.signs { width: 100%; border-collapse: separate; border-spacing: 18px 0; margin-top: 34px; page-break-inside: avoid; }
        table.signs td {
            width: 25%;
            text-align: center;
            padding: 6px;
            vertical-align: top;
            font-weight: bold;
            color: #334155;
        }
    </style>
</head>

<body>
    <htmlpagefooter name="page-footer">
        <table style="width:100%; border-top:1px solid #e2e8f0; font-size:8.5px; color:#94a3b8;">
            <tr>
                <td style="text-align:right;">{{ $survey->title }} — این گزارش به‌صورت خودکار توسط سامانه تولید شده است.</td>
                <td style="text-align:left; width:80px;">صفحه {PAGENO} از {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>
    <sethtmlpagefooter name="page-footer" value="on" />

    @php
        $fmtDate = fn ($d) => $d ? verta(\Illuminate\Support\Carbon::parse($d)->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') : '—';
        $eventTitle = $event->name ?? $event->title ?? '—';
    @endphp

    <table class="brand">
        <tr>
            <td>
                <div class="org">{{ $group->title ?? '—' }}</div>
                <div class="doc">گزارش رسمی نتایج نظرسنجی · {{ $eventTitle }}</div>
            </td>
            <td class="date">تاریخ تهیه گزارش<br><strong>{{ verta(now('Asia/Tehran'))->format('Y/m/d H:i') }}</strong></td>
        </tr>
    </table>

    <div class="title-box">
        <div class="t">{{ $survey->title }}</div>
        @if ($survey->description)
            <div class="s">{{ $survey->description }}</div>
        @endif
        <div class="s">بازه برگزاری: {{ $fmtDate($survey->start_at) }} تا {{ $fmtDate($survey->end_at) }}</div>
    </div>

    <table class="kpis">
        <tr>
            <td>
                <div class="kv">{{ number_format($responseCount ?? 0) }}</div>
                <div class="kl">تعداد پاسخ‌دهندگان</div>
            </td>
            <td>
                <div class="kv">{{ number_format($stats->count()) }}</div>
                <div class="kl">تعداد سؤالات</div>
            </td>
            <td>
                <div class="kv" style="font-size:12px;">{{ $isWeighted ? 'بر اساس سهام' : 'هر نفر یک رأی' }}</div>
                <div class="kl">مبنای شمارش</div>
            </td>
            <td>
                <div class="kv" style="font-size:12px;">{{ $survey->is_anonymous ? 'ناشناس' : 'با نام' }}</div>
                <div class="kl">نوع نظرسنجی</div>
            </td>
        </tr>
    </table>

    @if ($isWeighted)
        <div class="note">
            وزن هر رأی برابر است با: سهام عادی + (سهام ممتاز × {{ number_format((float) ($group->prefered_stock_weight ?? 0)) }})، به‌همراه سهام موکلانی که به رأی‌دهنده وکالت داده‌اند.
        </div>
    @endif

    @forelse ($stats->values() as $qIndex => $items)
        @php
            $rows = $items->sortByDesc(fn ($r) => (float) ($r->count ?? 0))->values();
            $maxValue = (float) $rows->max(fn ($r) => (float) ($r->count ?? 0));
            $totalVotes = $rows->sum(fn ($r) => (int) ($r->vote_count ?? $r->count ?? 0));
            $totalWeight = $rows->sum(fn ($r) => (float) ($r->count ?? 0));
        @endphp
        <div class="q">
            <table class="q-head">
                <tr>
                    <td class="q-num">{{ $qIndex + 1 }}</td>
                    <td class="q-title">{{ $items->first()->question_title }}</td>
                    <td class="q-meta">{{ number_format($totalVotes) }} رأی</td>
                </tr>
            </table>
            <table class="opts">
                <thead>
                    <tr>
                        <th class="c" style="width:34px;">رتبه</th>
                        <th>گزینه</th>
                        <th class="c" style="width:58px;">تعداد رأی</th>
                        @if ($isWeighted)
                            <th class="c" style="width:78px;">مجموع سهام</th>
                        @endif
                        <th class="c" style="width:52px;">درصد</th>
                        <th style="width:140px;">سهم از کل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $i => $row)
                        @php
                            $value = (float) ($row->count ?? 0);
                            $pct = (float) ($row->percent ?? 0);
                            $isWinner = $maxValue > 0 && $value === $maxValue;
                            $barPct = max(1, min(100, (int) round($pct)));
                        @endphp
                        <tr class="{{ $isWinner ? 'winner' : '' }}">
                            <td class="c num">{{ $i + 1 }}</td>
                            <td>
                                {{ $row->option_title ?? 'پاسخ متنی' }}
                                @if ($isWinner)
                                    &nbsp;<span class="badge">بیشترین رأی</span>
                                @endif
                            </td>
                            <td class="c num">{{ number_format((int) ($row->vote_count ?? $row->count ?? 0)) }}</td>
                            @if ($isWeighted)
                                <td class="c num">{{ number_format($value) }}</td>
                            @endif
                            <td class="c pct">{{ number_format($pct, 1) }}٪</td>
                            <td>
                                @php
                                    $fill = $isWinner ? '#16a34a' : '#2563eb';
                                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="10"><rect width="200" height="10" rx="5" fill="#e2e8f0"/><rect x="'.(200 - 2 * $barPct).'" width="'.(2 * $barPct).'" height="10" rx="5" fill="'.$fill.'"/></svg>';
                                @endphp
                                <img src="data:image/svg+xml;base64,{{ base64_encode($svg) }}" style="width:130px; height:7px;" alt="">
                            </td>
                        </tr>
                    @endforeach
                    <tr class="total">
                        <td></td>
                        <td>جمع کل</td>
                        <td class="c">{{ number_format($totalVotes) }}</td>
                        @if ($isWeighted)
                            <td class="c">{{ number_format($totalWeight) }}</td>
                        @endif
                        <td class="c">۱۰۰٪</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <div class="note" style="text-align:center;">هنوز پاسخی برای این نظرسنجی ثبت نشده است.</div>
    @endforelse

    <table class="signs">
        <tr>
            @foreach (['رئیس جلسه', 'ناظر اول', 'ناظر دوم', 'منشی'] as $role)
                <td>{{ $role }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach (range(1, 4) as $_)
                <td style="height:48px; border-bottom:1px dashed #94a3b8;">&nbsp;</td>
            @endforeach
        </tr>
    </table>
</body>

</html>
