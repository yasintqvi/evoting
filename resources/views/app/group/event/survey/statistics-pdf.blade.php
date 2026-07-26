<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش آمار نظرسنجی — {{ $survey->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
            text-align: right;
            color: #222;
            font-size: 11px;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }

        .meta {
            margin-top: 6px;
            color: #555;
            font-size: 10px;
            line-height: 1.6;
        }

        .note {
            margin: 10px 0;
            padding: 8px 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 10px;
            color: #444;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 18px;
        }

        table.results th,
        table.results td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: middle;
        }

        table.results th {
            background: #eee;
            font-weight: bold;
        }

        .q-title {
            margin: 14px 0 6px;
            font-size: 12px;
            font-weight: bold;
            color: #111;
        }

        .text-center {
            text-align: center;
        }

        .bar-wrap {
            width: 100%;
            height: 10px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }

        .bar {
            height: 10px;
            background: #0d6efd;
        }

        .footer {
            margin-top: 16px;
            font-size: 9px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>گزارش آمار نظرسنجی</h2>
        <p style="margin-top:6px;">{{ $survey->title }}</p>
        <div class="meta">
            <div>گروه: {{ $group->title ?? '—' }}</div>
            <div>رویداد: {{ $event->title ?? '—' }}</div>
            <div>تاریخ گزارش: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    @if ($isWeighted)
        <div class="note">
            آمار بر اساس <strong>سهام عادی + (سهام ممتاز × {{ number_format((float) ($group->prefered_stock_weight ?? 0)) }})</strong> هر شرکت‌کننده در این رویداد وزن‌دهی شده است.
        </div>
    @endif

    @forelse ($stats as $questionId => $items)
        @php
            $questionTitle = $items->first()->question_title;
            $valueLabel = $isWeighted ? 'وزن (سهم)' : 'تعداد';
        @endphp
        <div class="q-title">{{ $questionTitle }}</div>
        <table class="results">
            <thead>
                <tr>
                    <th style="width: 40px;">ردیف</th>
                    <th>گزینه</th>
                    <th class="text-center" style="width: 90px;">{{ $valueLabel }}</th>
                    <th class="text-center" style="width: 70px;">درصد</th>
                    <th style="width: 140px;">نمود</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $idx => $row)
                    @php
                        $label = $row->option_title ?? 'پاسخ متنی';
                        $count = $row->count ?? 0;
                        $pct = (float) ($row->percent ?? 0);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $label }}</td>
                        <td class="text-center">{{ number_format((float) $count, $isWeighted ? 2 : 0) }}</td>
                        <td class="text-center">{{ number_format($pct, 1) }}٪</td>
                        <td>
                            <div class="bar-wrap">
                                <div class="bar" style="width: {{ min(100, max(0, $pct)) }}%;"></div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p style="color:#666;">هنوز پاسخی ثبت نشده است.</p>
    @endforelse

    <div class="footer">
        این گزارش به‌صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
