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

        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>گزارش تفصیلی آرای ثبت شده</h2>
        <p style="margin-top:6px;">{{ $election->title }}</p>
        <div class="meta">
            <span>گروه: {{ $group->title ?? '-' }}</span>
            |
            <span>رویداد: {{ $event->title ?? '-' }}</span>
            |
            <span>تاریخ گزارش: {{ now()->format('Y-m-d H:i') }}</span>
        </div>
    </div>

    <h3 style="margin-bottom:8px;">لیست رای‌دهندگان و تعداد رای</h3>
    @if ($detailedVotes->count() > 0)
        <table class="results">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">ردیف</th>
                    <th class="text-center">کد ملی</th>
                    <th>کاندیدای انتخاب شده</th>
                    <th class="text-center" style="width: 100px;">تعداد رای</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detailedVotes as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center" dir="ltr">{{ $item['masked_national_code'] }}</td>
                        <td>{{ $item['candidate_name'] }}</td>
                        <td class="text-center">{{ number_format($item['vote_chunk']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">هنوز رای‌ای برای این انتخابات ثبت نشده است.</p>
    @endif

    <div class="footer">
        این گزارش به صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
