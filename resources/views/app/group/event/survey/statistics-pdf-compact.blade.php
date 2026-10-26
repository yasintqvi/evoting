<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>خلاصه نتایج نظرسنجی — {{ $survey->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #1f2937;
            font-size: 9.5px;
        }

        h1, h2, h3, p { margin: 0; padding: 0; }

        .brand { width: 100%; border-collapse: collapse; background: #1e3a8a; color: #fff; }
        .brand td { padding: 11px 14px; vertical-align: middle; }
        .org { font-size: 13px; font-weight: bold; }
        .doc { font-size: 9px; color: #c7d2fe; margin-top: 2px; }
        .date { text-align: left; font-size: 8.5px; color: #e0e7ff; }

        .title-box { margin-top: 10px; padding: 8px 12px; border-right: 4px solid #2563eb; background: #f1f5f9; }
        .tt { font-size: 12px; font-weight: bold; color: #0f172a; }
        .ts { font-size: 9px; color: #475569; margin-top: 3px; }

        table.grid { width: 100%; border-collapse: separate; border-spacing: 8px 8px; margin: 4px -8px 0; }
        td.cell { width: 50%; vertical-align: top; border: 1px solid #e2e8f0; padding: 0; }

        .q-head { background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 6px 9px; }
        .q-title { font-size: 10px; font-weight: bold; color: #0f172a; }
        .q-meta { font-size: 8px; color: #64748b; margin-top: 2px; }

        table.opts { width: 100%; border-collapse: collapse; }
        table.opts td { padding: 4px 9px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .opt { font-size: 9px; }
        .val { text-align: left; white-space: nowrap; font-size: 8.5px; color: #475569; }
        .pct { font-weight: bold; color: #1d4ed8; }
        .win { font-weight: bold; color: #15803d; }

        .note { margin-top: 8px; padding: 6px 9px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: 8.5px; }

        table.signs { width: 100%; border-collapse: separate; border-spacing: 16px 0; margin-top: 24px; page-break-inside: avoid; }
        table.signs td { width: 25%; text-align: center; font-weight: bold; color: #334155; padding: 4px; }
    </style>
</head>

<body>
    <htmlpagefooter name="page-footer">
        <table style="width:100%; border-top:1px solid #e2e8f0; font-size:8px; color:#94a3b8;">
            <tr>
                <td style="text-align:right;">{{ $survey->title }} — این گزارش به‌صورت خودکار توسط سامانه تولید شده است.</td>
                <td style="text-align:left; width:80px;">صفحه {PAGENO} از {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>
    <sethtmlpagefooter name="page-footer" value="on" />

    <table class="brand">
        <tr>
            <td>
                <div class="org">{{ $group->title ?? '—' }}</div>
                <div class="doc">خلاصه نتایج نظرسنجی · {{ $event->name ?? $event->title ?? '—' }}</div>
            </td>
            <td class="date">تاریخ تهیه گزارش<br><strong>{{ verta(now('Asia/Tehran'))->format('Y/m/d H:i') }}</strong></td>
        </tr>
    </table>

    <div class="title-box">
        <div class="tt">{{ $survey->title }}</div>
        <div class="ts">
            پاسخ‌دهندگان: {{ number_format($responseCount ?? 0) }}
            &nbsp;|&nbsp; سؤالات: {{ number_format($stats->count()) }}
            &nbsp;|&nbsp; مبنای شمارش: {{ $isWeighted ? 'بر اساس سهام' : 'هر نفر یک رأی' }}
        </div>
    </div>

    @if ($isWeighted)
        <div class="note">
            وزن هر رأی: سهام عادی + (سهام ممتاز × {{ number_format((float) ($group->prefered_stock_weight ?? 0)) }})، به‌همراه سهام موکلان رأی‌دهنده.
        </div>
    @endif

    @if ($stats->count() > 0)
        <table class="grid">
            @foreach ($stats->values()->chunk(2) as $pair)
                <tr>
                    @foreach ($pair as $qIndex => $items)
                        @php
                            $rows = $items->sortByDesc(fn ($r) => (float) ($r->count ?? 0))->values();
                            $maxValue = (float) $rows->max(fn ($r) => (float) ($r->count ?? 0));
                            $totalVotes = $rows->sum(fn ($r) => (int) ($r->vote_count ?? $r->count ?? 0));
                        @endphp
                        <td class="cell">
                            <div class="q-head">
                                <div class="q-title">{{ $qIndex + 1 }}. {{ $items->first()->question_title }}</div>
                                <div class="q-meta">{{ number_format($totalVotes) }} رأی</div>
                            </div>
                            <table class="opts">
                                @foreach ($rows as $row)
                                    @php
                                        $value = (float) ($row->count ?? 0);
                                        $pct = (float) ($row->percent ?? 0);
                                        $isWinner = $maxValue > 0 && $value === $maxValue;
                                        $barPct = max(1, min(100, (int) round($pct)));
                                        $fill = $isWinner ? '#16a34a' : '#2563eb';
                                        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="10"><rect width="200" height="10" rx="5" fill="#e2e8f0"/><rect x="'.(200 - 2 * $barPct).'" width="'.(2 * $barPct).'" height="10" rx="5" fill="'.$fill.'"/></svg>';
                                    @endphp
                                    <tr>
                                        <td class="opt {{ $isWinner ? 'win' : '' }}">{{ $row->option_title ?? 'پاسخ متنی' }}</td>
                                        <td style="width:80px;"><img src="data:image/svg+xml;base64,{{ base64_encode($svg) }}" style="width:76px; height:6px;" alt=""></td>
                                        <td class="val">
                                            {{ number_format((int) ($row->vote_count ?? $row->count ?? 0)) }} رأی
                                            @if ($isWeighted)
                                                · {{ number_format($value) }} سهم
                                            @endif
                                            · <span class="pct">{{ number_format($pct, 1) }}٪</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    @endforeach
                    @if ($pair->count() === 1)
                        <td style="width:50%;"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @else
        <div class="note" style="text-align:center;">هنوز پاسخی برای این نظرسنجی ثبت نشده است.</div>
    @endif

    <table class="signs">
        <tr>
            @foreach (['رئیس جلسه', 'ناظر اول', 'ناظر دوم', 'منشی'] as $role)
                <td>{{ $role }}</td>
            @endforeach
        </tr>
        <tr>
            @foreach (range(1, 4) as $_)
                <td style="height:40px; border-bottom:1px dashed #94a3b8;">&nbsp;</td>
            @endforeach
        </tr>
    </table>
</body>

</html>
