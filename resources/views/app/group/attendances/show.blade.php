@extends('app.layouts.app')

@section('head-tag')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@500;600;700;800&display=swap');

        .proj {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
            color: #1e293b;
            padding: 0.85rem 1rem 1.25rem;
            max-width: 1280px;
            margin: 0 auto;
        }

        .proj-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.85rem;
        }

        .proj-header h1 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-header p {
            margin: 0.15rem 0 0;
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .proj-live {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            padding: 0.3rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .proj-live::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: proj-pulse 1.4s infinite;
        }

        @keyframes proj-pulse {
            50% { opacity: 0.3; }
        }

        /* راست: جدول انتخابات | چپ: حضور */
        .proj-main {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 0.85rem;
            margin-bottom: 0.85rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .proj-main {
                grid-template-columns: 1fr;
            }
        }

        .proj-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            padding: 0.85rem 1rem 1rem;
        }

        .proj-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
        }

        .proj-panel-head h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-filter {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .proj-filter label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            margin: 0;
        }

        .proj-filter select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.3rem 0.55rem;
            font-size: 0.8rem;
            font-family: inherit;
            font-weight: 600;
            color: #0f172a;
            background: #f8fafc;
            min-width: 150px;
        }

        .proj-table-wrap {
            overflow: auto;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }

        .proj-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .proj-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            text-align: right;
            padding: 0.65rem 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .proj-table td {
            padding: 0.8rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-weight: 600;
        }

        .proj-table tr:last-child td {
            border-bottom: none;
        }

        .proj-table .name {
            font-weight: 800;
            color: #0f172a;
            font-size: 0.95rem;
        }

        .proj-status {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            color: #fff;
            border-radius: 6px;
            padding: 0.2rem 0.5rem;
            white-space: nowrap;
        }

        .proj-status.live { background: #16a34a; }
        .proj-status.wait { background: #ea580c; }

        .proj-pct {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-meta {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 600;
        }

        .proj-bar {
            width: 110px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 999px;
            overflow: hidden;
            margin-top: 0.35rem;
        }

        .proj-bar > i {
            display: block;
            height: 100%;
            background: #16a34a;
            border-radius: 999px;
        }

        .proj-bar.wait > i {
            background: #ea580c;
            width: 0 !important;
        }

        .proj-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 1.25rem 1rem;
            text-align: center;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.875rem;
            background: #f8fafc;
        }

        /* حضور: سمت چپ صفحه، فشرده + نمودار بزرگ‌تر */
        .proj-attendance {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.85rem;
            padding: 1rem 0.85rem;
        }

        .proj-chart {
            width: 140px;
            height: 140px;
            position: relative;
        }

        .proj-stats-mini {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .proj-stat-mini {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.45rem 0.6rem;
            background: #f8fafc;
        }

        .proj-stat-mini.present {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .proj-stat-mini.absent {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .proj-stat-mini .side {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .proj-stat-mini .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .proj-stat-mini.present .dot { background: #16a34a; }
        .proj-stat-mini.absent .dot { background: #dc2626; }

        .proj-stat-mini .lbl {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
        }

        .proj-stat-mini .val {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1;
        }

        .proj-stat-mini.present .val { color: #16a34a; }
        .proj-stat-mini.absent .val { color: #dc2626; }

        .proj-people-panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            padding: 0.75rem 1rem 0.9rem;
        }

        .proj-people-panel h2 {
            margin: 0 0 0.6rem;
            font-size: 0.9rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-people {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.4rem;
            max-height: 160px;
            overflow: auto;
        }

        .proj-person {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.35rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.35rem 0.5rem;
            background: #fff;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .proj-person.on {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .proj-person .tag {
            font-size: 0.65rem;
            font-weight: 700;
            border-radius: 5px;
            padding: 0.1rem 0.35rem;
            white-space: nowrap;
        }

        .proj-person.on .tag {
            background: #16a34a;
            color: #fff;
        }

        .proj-person.off .tag {
            background: #fee2e2;
            color: #b91c1c;
        }

        .proj-person .name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }
    </style>
@endsection

@section('content')
    <div class="proj">
        <div class="proj-header">
            <div>
                <h1>{{ $event->title }}</h1>
                <p>نمایش زنده جلسه برای پروژکتور</p>
            </div>
            <span class="proj-live">آمار زنده</span>
        </div>

        <div class="proj-main">
            {{-- در RTL ستون اول سمت راست می‌آید: جدول انتخابات --}}
            <div class="proj-panel">
                <div class="proj-panel-head">
                    <h2>انتخابات</h2>
                    <div class="proj-filter">
                        <label for="election-filter">فیلتر</label>
                        <select id="election-filter">
                            <option value="all">همه</option>
                            <option value="ongoing">در حال برگزاری</option>
                            <option value="waiting">شروع‌نشده</option>
                        </select>
                    </div>
                </div>
                <div class="proj-table-wrap">
                    <table class="proj-table">
                        <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>وضعیت</th>
                                <th>پیشرفت</th>
                                <th>رأی‌دهندگان</th>
                            </tr>
                        </thead>
                        <tbody id="elections-tbody">
                            <tr>
                                <td colspan="4"><div class="proj-empty">در حال بارگذاری...</div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ستون دوم در RTL سمت چپ: حضور --}}
            <div class="proj-panel proj-attendance">
                <div class="proj-chart" dir="ltr">
                    <canvas id="attendanceChart"></canvas>
                </div>
                <div class="proj-stats-mini">
                    <div class="proj-stat-mini present">
                        <div class="side">
                            <span class="dot"></span>
                            <span class="lbl">حاضر</span>
                        </div>
                        <span class="val" id="present-count">{{ toPersianNum($event->present_count ?? 0) }}</span>
                    </div>
                    <div class="proj-stat-mini absent">
                        <div class="side">
                            <span class="dot"></span>
                            <span class="lbl">غایب</span>
                        </div>
                        <span class="val" id="absent-count">{{ toPersianNum($event->absent_count ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="proj-people-panel">
            <h2>لیست حاضرین و غایبین</h2>
            <div class="proj-people" id="attendees-list">
                <div class="proj-empty" style="grid-column:1/-1;padding:0.85rem;">در حال بارگذاری...</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function toPersianNum(str) {
            const map = { '0': '۰', '1': '۱', '2': '۲', '3': '۳', '4': '۴', '5': '۵', '6': '۶', '7': '۷', '8': '۸', '9': '۹' };
            return String(str).replace(/\d/g, w => map[w]);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['حاضر', 'غایب'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#16a34a', '#dc2626'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false }
                    }
                }
            });

            const presentEl = document.getElementById('present-count');
            const absentEl = document.getElementById('absent-count');
            const peopleEl = document.getElementById('attendees-list');
            const tbodyEl = document.getElementById('elections-tbody');
            const filterEl = document.getElementById('election-filter');
            const eventSlug = @json($event->slug);

            let electionsCache = { ongoing: [], waiting: [] };

            function renderElections() {
                const filter = filterEl.value;
                let rows = [];

                if (filter === 'all' || filter === 'ongoing') {
                    rows = rows.concat(electionsCache.ongoing.map(e => ({ ...e, kind: 'live' })));
                }
                if (filter === 'all' || filter === 'waiting') {
                    rows = rows.concat(electionsCache.waiting.map(e => ({ ...e, kind: 'wait' })));
                }

                if (!rows.length) {
                    tbodyEl.innerHTML = '<tr><td colspan="4"><div class="proj-empty">موردی برای نمایش نیست</div></td></tr>';
                    return;
                }

                tbodyEl.innerHTML = rows.map(e => {
                    const pct = e.kind === 'live' ? Number(e.percent || 0) : 0;
                    const voters = e.kind === 'live'
                        ? `${toPersianNum(e.voted_count)} از ${toPersianNum(e.eligible_count)}`
                        : '—';

                    return `
                        <tr>
                            <td><div class="name">${e.title}</div></td>
                            <td><span class="proj-status ${e.kind}">${e.status_label}</span></td>
                            <td>
                                <div class="proj-pct">${e.kind === 'live' ? toPersianNum(pct) + '٪' : '—'}</div>
                                ${e.kind === 'live' ? `<div class="proj-bar"><i style="width:${pct}%"></i></div>` : `<div class="proj-bar wait"><i></i></div>`}
                            </td>
                            <td><div class="proj-meta">${voters}</div></td>
                        </tr>
                    `;
                }).join('');
            }

            function fetchAttendance() {
                fetch(`/events/${eventSlug}/attendance-stats`)
                    .then(r => r.json())
                    .then(data => {
                        presentEl.textContent = toPersianNum(data.present);
                        absentEl.textContent = toPersianNum(data.absent);
                        chart.data.datasets[0].data = [data.present, data.absent];
                        chart.update('none');

                        if (!data.attendees || !data.attendees.length) {
                            peopleEl.innerHTML = '<div class="proj-empty" style="grid-column:1/-1;padding:0.85rem;">شرکت‌کننده‌ای ثبت نشده است</div>';
                            return;
                        }

                        peopleEl.innerHTML = data.attendees.map(a => `
                            <div class="proj-person ${a.is_present ? 'on' : 'off'}">
                                <span class="name">${a.name}</span>
                                <span class="tag">${a.is_present ? 'حاضر' : 'غایب'}</span>
                            </div>
                        `).join('');
                    })
                    .catch(console.error);
            }

            function fetchElections() {
                fetch(`/events/${eventSlug}/live-election-stats`)
                    .then(r => r.json())
                    .then(data => {
                        electionsCache = {
                            ongoing: data.ongoing || [],
                            waiting: data.waiting || []
                        };
                        renderElections();
                    })
                    .catch(console.error);
            }

            filterEl.addEventListener('change', renderElections);

            fetchAttendance();
            fetchElections();
            setInterval(fetchAttendance, 5000);
            setInterval(fetchElections, 5000);
        });
    </script>
@endsection
