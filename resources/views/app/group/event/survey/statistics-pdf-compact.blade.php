<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش آمار نظرسنجی (فشرده) — {{ $survey->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #222;
            font-size: 9px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 6px;
        }

        .header h2 {
            font-size: 14px;
        }

        .meta {
            margin-top: 4px;
            color: #555;
            font-size: 8px;
            line-height: 1.5;
        }

        .note {
            margin: 6px 0 10px;
            padding: 5px 8px;
            background: #f8f9fa;
            border-radius: 3px;
            font-size: 8px;
            color: #444;
        }

        table.grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }

        table.grid>tbody>tr>td.cell {
            width: 50%;
            vertical-align: top;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 8px;
        }

        .q-title {
            font-size: 10px;
            font-weight: bold;
            color: #111;
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
        }

        table.opts {
            width: 100%;
            border-collapse: collapse;
        }

        table.opts td {
            padding: 2px 0;
            font-size: 8px;
            vertical-align: middle;
        }

        table.opts td.opt-label {
            width: 40%;
            color: #333;
        }

        table.opts td.opt-bar {
            width: 38%;
        }

        table.opts td.opt-value {
            width: 22%;
            text-align: left;
            color: #555;
            white-space: nowrap;
            direction: ltr;
        }

        .bar-wrap {
            width: 100%;
            height: 6px;
            background: #eee;
            border-radius: 2px;
        }

        .bar {
            height: 6px;
            background: #0d6efd;
        }

        .footer {
            margin-top: 14px;
            font-size: 8px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>گزارش آمار نظرسنجی (فشرده)</h2>
        <p style="margin-top:4px;">{{ $survey->title }}</p>
        <div class="meta">
            <span>گروه: {{ $group->title ?? '—' }}</span>
            |
            <span>رویداد: {{ $event->title ?? '—' }}</span>
            |
            <span>تاریخ گزارش: {{ now()->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    @if ($isWeighted)
        <div class="note">
            آمار بر اساس <strong>مجموع سهام عادی و ممتاز</strong> هر شرکت‌کننده در این رویداد وزن‌دهی شده است.
        </div>
    @endif

    @if ($stats->count() > 0)
        <table class="grid">
            @foreach ($stats->chunk(2) as $pair)
                <tr>
                    @foreach ($pair as $items)
                        @php
                            $questionTitle = $items->first()->question_title;
                        @endphp
                        <td class="cell">
                            <div class="card">
                                <div class="q-title">{{ $questionTitle }}</div>
                                <table class="opts">
                                    @foreach ($items as $row)
                                        @php
                                            $label = $row->option_title ?? 'پاسخ متنی';
                                            $count = $row->count ?? 0;
                                            $pct = (float) ($row->percent ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="opt-label">{{ $label }}</td>
                                            <td class="opt-bar">
                                                <div class="bar-wrap">
                                                    <div class="bar" style="width: {{ min(100, max(0, $pct)) }}%;">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="opt-value">
                                                {{ number_format((float) $count, $isWeighted ? 1 : 0) }} ({{ number_format($pct, 1) }}٪)
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </td>
                    @endforeach
                    @if ($pair->count() === 1)
                        <td class="cell"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @else
        <p style="color:#666;">هنوز پاسخی ثبت نشده است.</p>
    @endif

    <div class="footer">
        این گزارش به‌صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
