<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش حضور و وکالت - {{ $election->title }}</title>
    <style>
        body {
            font-family: vazir, sans-serif;
            direction: rtl;
            text-align: right;
            color: #222;
            font-size: 12px;
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
            border-bottom: 1px solid #ddd;
            padding-bottom: 8px;
        }

        .meta {
            margin-top: 6px;
            color: #666;
            font-size: 11px;
        }

        .stats {
            margin: 16px 0;
            width: 100%;
        }

        .stats td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table.results {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.results th,
        table.results td {
            border: 1px solid #ddd;
            padding: 6px 8px;
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

        h3.section-title {
            margin: 18px 0 8px;
        }

        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="header" style="text-align:center;">
        <h1 style="font-size:20px;">{{ $group->title ?? '-' }}</h1>
        <h2 style="margin-top:6px;">گزارش حاضرین در جلسه</h2>
        <p style="margin-top:6px;">{{ $event->name ?? '-' }}</p>
        <div class="meta">
            <span>تاریخ گزارش:
                {{ function_exists('verta') ? verta(now('Asia/Tehran'))->format('Y/m/d H:i') : now('Asia/Tehran')->format('Y-m-d H:i') }}
            </span>
        </div>
        {{-- <div class="meta" style="margin-top:4px;">
            وضعیت حضور بر اساس زمان شروع این انتخابات محاسبه شده است.
            @if ($election->starts_at)
                (شروع: {{ function_exists('verta') ? verta($election->starts_at->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i') : $election->starts_at }})
            @endif
        </div> --}}
    </div>

    <table class="stats">
        <tr>
            <td>
                <strong>{{ $presentParticipants->count() }}</strong><br>
                <span class="muted">حاضر در جلسه</span>
            </td>
            <td>
                <strong>{{ $absentParticipants->count() }}</strong><br>
                <span class="muted">غایب در جلسه</span>
            </td>
            <td>
                <strong>{{ $proxies->count() }}</strong><br>
                <span class="muted">تعداد وکالت‌ها</span>
            </td>
        </tr>
    </table>

    <h3 class="section-title">افراد حاضر در جلسه</h3>

    @if ($presentParticipants->count() > 0)
        <table class="results">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">ردیف</th>
                    <th>نام و نام خانوادگی</th>
                    <th class="text-center" style="width: 130px;">سهم عادی</th>
                    <th class="text-center" style="width: 130px;">سهم ممتاز</th>
                    <th>وکیل از طرف</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presentParticipants as $index => $participant)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participant->user?->full_name ?? '-' }}</td>
                        <td class="text-center">{{ number_format($participant->normal_stock_count) }}</td>
                        <td class="text-center">{{ number_format($participant->prefered_stock_count) }}</td>
                        <td>
                            @if ($participant->attorney)
                                {{ $participant->attorney->user?->full_name ?? '-' }}
                                <span class="muted">
                                    (سهم عادی: {{ number_format($participant->normal_stock_count) }} -
                                    سهم ممتاز: {{ number_format($participant->prefered_stock_count) }})
                                </span>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">هیچ فردی حاضر ثبت نشده است.</p>
    @endif

    <h3 class="section-title">افراد غایب در جلسه</h3>

    @if ($absentParticipants->count() > 0)
        <table class="results">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">ردیف</th>
                    <th>نام و نام خانوادگی</th>
                    <th class="text-center" style="width: 130px;">سهم عادی</th>
                    <th class="text-center" style="width: 130px;">سهم ممتاز</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($absentParticipants as $index => $participant)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participant->user?->full_name ?? '-' }}</td>
                        <td class="text-center">{{ number_format($participant->normal_stock_count) }}</td>
                        <td class="text-center">{{ number_format($participant->prefered_stock_count) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">همه شرکت‌کنندگان حاضر بوده‌اند.</p>
    @endif

    <h3 class="section-title">فهرست وکالت‌ها</h3>

    @if ($proxies->count() > 0)
        <table class="results">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">ردیف</th>
                    <th>موکل (صاحب سهم)</th>
                    <th>وکیل (رأی‌دهنده)</th>
                    <th class="text-center" style="width: 130px;">سهم عادی موکل</th>
                    <th class="text-center" style="width: 130px;">سهم ممتاز موکل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($proxies as $index => $participant)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $participant->user?->full_name ?? '-' }}</td>
                        <td>{{ $participant->attorney?->user?->full_name ?? '-' }}</td>
                        <td class="text-center">{{ number_format($participant->normal_stock_count) }}</td>
                        <td class="text-center">{{ number_format($participant->prefered_stock_count) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">هیچ وکالتی ثبت نشده است.</p>
    @endif

    <div class="footer">
        این گزارش به صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
