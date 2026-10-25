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
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-header p {
            margin: 0.15rem 0 0;
            font-size: 1.05rem;
            color: #64748b;
            font-weight: 600;
        }

        .proj-live {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            padding: 0.45rem 1rem;
            font-size: 1.05rem;
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

        .proj-quorum {
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 2px solid #fcd34d;
            border-radius: 12px;
            background: #fffbeb;
            color: #92400e;
        }

        .proj-quorum.official {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
        }

        .proj-quorum h2 { font-size: 2.1rem; font-weight: 800; color: inherit; }
        .proj-quorum div { font-size: 1.1rem; font-weight: 700; }
        .proj-quorum p { margin: 0.5rem 0 0; font-size: 1.15rem; font-weight: 600; }

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
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-filter {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .proj-filter label {
            font-size: 1rem;
            font-weight: 700;
            color: #64748b;
            margin: 0;
        }

        .proj-filter select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.4rem 0.65rem;
            font-size: 1rem;
            font-family: inherit;
            font-weight: 700;
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
            font-size: 1.1rem;
        }

        .proj-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            font-size: 1rem;
            text-align: right;
            padding: 0.75rem 0.85rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        .proj-table td {
            padding: 0.9rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-weight: 700;
        }

        .proj-table tr:last-child td {
            border-bottom: none;
        }

        .proj-table .name {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.2rem;
        }

        .proj-status {
            display: inline-block;
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            border-radius: 6px;
            padding: 0.3rem 0.65rem;
            white-space: nowrap;
        }

        .proj-status.live { background: #16a34a; }
        .proj-status.wait { background: #ea580c; }

        .proj-pct {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-meta {
            font-size: 1.05rem;
            color: #64748b;
            font-weight: 700;
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
            font-weight: 700;
            font-size: 1.1rem;
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
            width: 170px;
            height: 170px;
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
            font-size: 1rem;
            font-weight: 700;
            color: #64748b;
        }

        .proj-stat-mini .val {
            font-size: 1.5rem;
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
            margin: 0;
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
        }

        .proj-people-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-bottom: 0.6rem;
        }

        .proj-people {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 0.5rem;
            max-height: 220px;
            overflow: auto;
        }

        .proj-person {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.4rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 0.65rem;
            background: #fff;
            font-size: 1rem;
            font-weight: 700;
        }

        .proj-person.on {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .proj-person .tag {
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 5px;
            padding: 0.15rem 0.45rem;
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

        .proj-people-subhead {
            font-size: 0.85rem;
            font-weight: 800;
            color: #64748b;
            padding: 0.25rem 0.1rem 0.1rem;
            margin-top: 0.1rem;
        }

        .proj-people-subhead:first-child {
            margin-top: 0;
        }
    </style>
@endsection

@section('content')
    <div class="proj">
        <div class="proj-header">
            <div>
                <h1>{{ $event->title }}</h1>
                {{-- <p>نمایش زنده جلسه برای پروژکتور</p> --}}
            </div>
            <span class="proj-live">آمار زنده</span>
        </div>

        @php($quorum = $event->attendanceQuorum())
        <section id="meeting-quorum" class="proj-quorum {{ $quorum['is_official'] ? 'official' : '' }}" aria-live="polite" aria-atomic="true">
            <div>وضعیت رسمیت جلسه · حد نصاب: {{ toPersianNum(number_format($quorum['quorum_percent'])) }}٪ سهام</div>
            <h2 id="quorum-status" class="mt-2 mb-0">{{ $quorum['is_official'] ? 'جلسه رسمی است' : 'جلسه رسمی نشده است' }}</h2>
            <p id="quorum-details">سهام حاضر: {{ toPersianNum(number_format($quorum['present_shares'])) }} از {{ toPersianNum(number_format($quorum['total_shares'])) }} سهم ({{ toPersianNum(number_format($quorum['percent'], 2)) }}٪)</p>
            <p id="quorum-update" class="small" role="status"></p>
        </section>

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
            <div class="proj-people-head">
                <h2>لیست حاضرین و غایبین</h2>
                <div class="proj-filter">
                    <label for="attendees-sort">مرتب‌سازی</label>
                    <select id="attendees-sort">
                        <option value="presence">وضعیت حضور (حاضرین ابتدا)</option>
                        <option value="alphabetical">حروف الفبا</option>
                    </select>
                </div>
            </div>
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
            const peopleSortEl = document.getElementById('attendees-sort');
            const tbodyEl = document.getElementById('elections-tbody');
            const filterEl = document.getElementById('election-filter');
            const eventSlug = @json($event->slug);

            let electionsCache = { ongoing: [], waiting: [] };
            let attendeesCache = [];
            const nameCollator = new Intl.Collator('fa', { sensitivity: 'base' });
            const normalizedName = name => String(name ?? '').replace(/ي/g, 'ی').replace(/ك/g, 'ک').trim();

            function makePersonEl(a) {
                const person = document.createElement('div');
                person.className = `proj-person ${a.is_present ? 'on' : 'off'}`;
                const name = document.createElement('span');
                name.className = 'name';
                name.textContent = a.name;
                const tag = document.createElement('span');
                tag.className = 'tag';
                tag.textContent = a.is_present ? 'حاضر' : 'غایب';
                person.append(name, tag);
                return person;
            }

            function byName(a, b) {
                return nameCollator.compare(normalizedName(a.name), normalizedName(b.name));
            }

            function renderAttendees() {
                if (!attendeesCache.length) {
                    peopleEl.innerHTML = '<div class="proj-empty" style="grid-column:1/-1;padding:0.85rem;">شرکت‌کننده‌ای ثبت نشده است</div>';
                    return;
                }

                if (peopleSortEl.value === 'presence') {
                    const present = attendeesCache.filter(a => a.is_present)
                        .sort((a, b) => (a.present_at ?? 0) - (b.present_at ?? 0));
                    const absent = attendeesCache.filter(a => !a.is_present).sort(byName);

                    const nodes = [];

                    if (present.length) {
                        const heading = document.createElement('div');
                        heading.className = 'proj-people-subhead';
                        heading.style.gridColumn = '1/-1';
                        heading.textContent = `حاضرین (${present.length})`;
                        nodes.push(heading, ...present.map(makePersonEl));
                    }

                    if (absent.length) {
                        const heading = document.createElement('div');
                        heading.className = 'proj-people-subhead';
                        heading.style.gridColumn = '1/-1';
                        heading.textContent = `غایبین (${absent.length})`;
                        nodes.push(heading, ...absent.map(makePersonEl));
                    }

                    peopleEl.replaceChildren(...nodes);
                    return;
                }

                const attendees = [...attendeesCache].sort(byName);
                peopleEl.replaceChildren(...attendees.map(makePersonEl));
            }

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
                    .then(r => {
                        if (!r.ok) throw new Error('Attendance request failed');
                        return r.json();
                    })
                    .then(data => {
                        const quorum = data.quorum;
                        document.getElementById('meeting-quorum').classList.toggle('official', quorum.is_official);
                        document.getElementById('quorum-status').textContent = quorum.is_official
                            ? 'جلسه رسمی است' : 'جلسه رسمی نشده است';
                        const format = value => Number(value).toLocaleString('fa-IR', { maximumFractionDigits: 2 });
                        document.getElementById('quorum-details').textContent =
                            `سهام حاضر: ${format(quorum.present_shares)} از ${format(quorum.total_shares)} سهم (${format(quorum.percent)}٪)`;
                        document.getElementById('quorum-update').textContent = '';
                        presentEl.textContent = toPersianNum(data.present);
                        absentEl.textContent = toPersianNum(data.absent);
                        chart.data.datasets[0].data = [data.present, data.absent];
                        chart.update('none');

                        attendeesCache = data.attendees || [];
                        renderAttendees();
                    })
                    .catch(error => {
                        document.getElementById('quorum-update').textContent = 'به‌روزرسانی انجام نشد؛ وضعیت نمایش‌داده‌شده مربوط به آخرین اطلاعات دریافتی است.';
                        console.error(error);
                    });
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
            peopleSortEl.addEventListener('change', renderAttendees);

            fetchAttendance();
            fetchElections();
            setInterval(fetchAttendance, 5000);
            setInterval(fetchElections, 5000);
        });
    </script>
@endsection
