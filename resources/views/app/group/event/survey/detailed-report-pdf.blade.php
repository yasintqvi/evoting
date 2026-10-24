<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>گزارش تفصیلی نظرسنجی — {{ $survey->title }}</title>
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
            background: #fff8e1;
            border: 1px solid #ffe08a;
            border-radius: 4px;
            font-size: 10px;
            color: #7a5b00;
        }

        table.respondent {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 18px;
            page-break-inside: avoid;
        }

        table.respondent th,
        table.respondent td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .respondent-head {
            background: #eef2ff;
        }

        .respondent-head .name {
            font-size: 12px;
            font-weight: bold;
            color: #111;
        }

        .respondent-head .national-code {
            font-size: 10px;
            color: #555;
        }

        .respondent-head .weight {
            font-size: 10px;
            color: #1d4ed8;
            font-weight: bold;
        }

        table.answers {
            width: 100%;
            border-collapse: collapse;
        }

        table.answers th,
        table.answers td {
            border: 1px solid #e3e3e3;
            padding: 5px 8px;
            font-size: 10px;
        }

        table.answers th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: right;
            width: 45%;
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
        <h2>گزارش تفصیلی نظرسنجی</h2>
        <p style="margin-top:6px;">{{ $survey->title }}</p>
        <div class="meta">
            <div>گروه: {{ $group->title ?? '—' }}</div>
            <div>رویداد: {{ $event->title ?? '—' }}</div>
            <div>تعداد پاسخ‌دهندگان: {{ $rows->count() }}</div>
            <div>تاریخ گزارش: {{ verta(now('Asia/Tehran'))->format('Y/m/d H:i') }}</div>
        </div>
    </div>

    <div class="note">
        این گزارش هویت پاسخ‌دهندگان را به‌صورت کامل نشان می‌دهد و فقط به این دلیل تولید شده که این نظرسنجی به‌صورت
        <strong>غیرناشناس</strong> تنظیم شده است. توزیع این گزارش باید مطابق سیاست‌های محرمانگی سازمان انجام شود.
    </div>

    @forelse ($rows as $row)
        <table class="respondent">
            <tr class="respondent-head">
                <td style="width:55%;">
                    <span class="name">{{ $row['name'] }}</span><br>
                    <span class="national-code">کد ملی: {{ $row['national_code'] }}</span>
                </td>
                <td style="width:45%; text-align:left;">
                    @if ($isWeighted)
                        <span class="weight">وزن سهام: {{ number_format($row['stock_weight'], 2) }}</span>
                    @endif
                </td>
            </tr>
        </table>
        <table class="answers" style="margin-bottom:14px;">
            @foreach ($row['answers'] as $answer)
                <tr>
                    <th>{{ $answer['question_text'] }}</th>
                    <td>{{ $answer['answer_text'] }}</td>
                </tr>
            @endforeach
        </table>
    @empty
        <p style="color:#666;">هنوز پاسخی ثبت نشده است.</p>
    @endforelse

    <div class="footer">
        این گزارش به‌صورت خودکار توسط سامانه تولید شده است.
    </div>
</body>

</html>
