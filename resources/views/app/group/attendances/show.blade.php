@extends('app.layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap');

        .dash-wrapper {
            font-family: 'Vazirmatn', sans-serif;
            direction: rtl;
            padding: 1.25rem 0.5rem;
        }

        /* ── Page Title ── */
        .dash-page-label {
            font-size: 12px;
            color: #6c757d;
            font-weight: 400;
            margin-bottom: 4px;
            letter-spacing: 0.03em;
        }

        .dash-page-title {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 1.25rem;
        }

        /* ── Hero Banner ── */
        .event-hero {
            background: linear-gradient(135deg, #534AB7 0%, #6f42c1 50%, #6610f2 100%);
            border-radius: 18px;
            padding: 1.75rem 2rem;
            margin-bottom: 1.5rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .event-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .event-hero::after {
            content: '';
            position: absolute;
            bottom: -70px;
            right: 20px;
            width: 240px;
            height: 240px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-icon-wrap {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .hero-body {
            position: relative;
            z-index: 1;
        }

        .hero-body h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .hero-body p {
            font-size: 13px;
            opacity: 0.8;
            font-weight: 300;
            margin-bottom: 0;
        }

        .badge-live {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 11px;
            margin-top: 10px;
        }

        .badge-live-dot {
            width: 7px;
            height: 7px;
            background: #4cff91;
            border-radius: 50%;
            animation: livePulse 1.5s infinite;
            flex-shrink: 0;
        }

        @keyframes livePulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        /* ── Main Grid ── */
        .dash-grid {
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 1rem;
            margin-bottom: 1rem;
            align-items: start;
        }

        @media (max-width: 900px) {
            .dash-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ── Cards ── */
        .d-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
        }

        .d-card-body {
            padding: 1.25rem 1.5rem;
        }

        .d-card-label {
            font-size: 11px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }

        /* ── Chart Card ── */
        .chart-canvas-wrap {
            height: 460px;
            position: relative;
        }

        /* ── Stats Column ── */
        .stats-col {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 1.1rem 1.4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: box-shadow 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
        }

        .stat-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            margin-bottom: 5px;
            letter-spacing: 0.03em;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .stat-value.present {
            color: #16a34a;
        }

        .stat-value.absent {
            color: #dc2626;
        }

        .stat-value.date {
            font-size: 17px;
            color: #1a1a2e;
            font-weight: 600;
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .icon-present {
            background: #dcfce7;
            color: #16a34a;
        }

        .icon-absent {
            background: #fee2e2;
            color: #dc2626;
        }

        .icon-date {
            background: #ede9fe;
            color: #7c3aed;
        }

        /* ── Progress Ratios Card ── */
        .ratio-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 1.1rem 1.4rem;
        }

        .ratio-row {
            margin-bottom: 0.85rem;
        }

        .ratio-row:last-child {
            margin-bottom: 0;
        }

        .ratio-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .ratio-name {
            font-size: 13px;
            color: #374151;
            font-weight: 500;
        }

        .ratio-pct {
            font-size: 13px;
            font-weight: 700;
        }

        .prog-track {
            height: 8px;
            background: #f1f5f9;
            border-radius: 99px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fill-present {
            background: linear-gradient(90deg, #16a34a, #4ade80);
        }

        .fill-absent {
            background: linear-gradient(90deg, #dc2626, #f87171);
        }
    </style>

    <div class="dash-wrapper">

        {{-- Page Header --}}
        <p class="dash-page-label">داشبورد رویداد / {{ $event->name }}</p>
        <div class="dash-page-title">نمایش نمودار جلسه</div>

        {{-- Hero Banner --}}
        <div class="event-hero">
            <div class="hero-icon-wrap">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="hero-body">
                <h2>{{ $event->title }}</h2>
                <p>{{ $event->description }}</p>
                <span class="badge-live">
                    <span class="badge-live-dot"></span>
                    آمار زنده
                </span>
            </div>
        </div>

        {{-- Main Dashboard Grid --}}
        <div class="dash-grid">

            {{-- Chart Card --}}
            <div class="d-card">
                <div class="d-card-body">
                    <div class="d-card-label">وضعیت حضور و غیاب</div>
                    <div class="chart-canvas-wrap" dir="ltr">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Stats Column --}}
            <div class="stats-col">

                {{-- Present --}}
                <div class="stat-card">
                    <div>
                        <div class="stat-label">تعداد حاضرین</div>
                        <div class="stat-value present" id="present-count">{{ toPersianNum($event->present_count) }}</div>
                    </div>
                    <div class="stat-icon icon-present">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>

                {{-- Absent --}}
                <div class="stat-card">
                    <div>
                        <div class="stat-label">تعداد غایبین</div>
                        <div class="stat-value absent" id="absent-count">{{ toPersianNum($event->absent_count) }}</div>
                    </div>
                    <div class="stat-icon icon-absent">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                </div>

                {{-- Date --}}
                <div class="stat-card">
                    <div>
                        <div class="stat-label">تاریخ جلسه</div>
                        <div class="stat-value date">{{ toPersianNum(verta($event->created_at)->format('Y/m/d')) }}</div>
                    </div>
                    <div class="stat-icon icon-date">
                        <i class="bi bi-calendar3"></i>
                    </div>
                </div>

                {{-- Progress Ratios --}}
                <div class="ratio-card">
                    <div class="d-card-label">نسبت حضور</div>

                    <div class="ratio-row">
                        <div class="ratio-meta">
                            <span class="ratio-name">حاضر</span>
                            <span class="ratio-pct" style="color:#16a34a;" id="present-pct">—</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill fill-present" id="present-bar" style="width: 0%;"></div>
                        </div>
                    </div>

                    <div class="ratio-row">
                        <div class="ratio-meta">
                            <span class="ratio-name">غایب</span>
                            <span class="ratio-pct" style="color:#dc2626;" id="absent-pct">—</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill fill-absent" id="absent-bar" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- /Stats Column --}}

        </div>
        {{-- /Main Grid --}}

        {{-- Elections Container (if needed) --}}
        <div class="row mt-3" id="elections-container"></div>

    </div>
    {{-- /dash-wrapper --}}
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function toPersianNum(str) {
            const map = {
                '0': '۰',
                '1': '۱',
                '2': '۲',
                '3': '۳',
                '4': '۴',
                '5': '۵',
                '6': '۶',
                '7': '۷',
                '8': '۸',
                '9': '۹'
            };
            return String(str).replace(/\d/g, w => map[w]);
        }

        document.addEventListener("DOMContentLoaded", function() {

            /* ── Chart Setup ── */
            const ctx = document.getElementById('attendanceChart').getContext('2d');

            const attendanceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['حاضر', 'غایب'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#16a34a', '#dc2626'],
                        hoverBackgroundColor: ['#15803d', '#b91c1c'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    family: 'Vazirmatn',
                                    size: 13
                                },
                                usePointStyle: true,
                                pointStyleWidth: 10
                            }
                        },
                        tooltip: {
                            rtl: true,
                            bodyFont: {
                                family: 'Vazirmatn'
                            },
                            titleFont: {
                                family: 'Vazirmatn'
                            }
                        }
                    }
                }
            });

            /* ── DOM References ── */
            const presentCountEl = document.getElementById('present-count');
            const absentCountEl = document.getElementById('absent-count');
            const presentPctEl = document.getElementById('present-pct');
            const absentPctEl = document.getElementById('absent-pct');
            const presentBarEl = document.getElementById('present-bar');
            const absentBarEl = document.getElementById('absent-bar');

            /* ── Update UI ── */
            function updateUI(present, absent) {
                const total = (present + absent) || 1;
                const pPct = Math.round((present / total) * 100);
                const aPct = Math.round((absent / total) * 100);

                presentCountEl.textContent = toPersianNum(present);
                absentCountEl.textContent = toPersianNum(absent);
                presentPctEl.textContent = toPersianNum(pPct) + '٪';
                absentPctEl.textContent = toPersianNum(aPct) + '٪';
                presentBarEl.style.width = pPct + '%';
                absentBarEl.style.width = aPct + '%';

                attendanceChart.data.datasets[0].data = [present, absent];
                attendanceChart.update('active');
            }

            /* ── Fetch Stats ── */
            const eventId = "{{ $event->slug }}";

            function fetchStats() {
                fetch(`/events/${eventId}/attendance-stats`)
                    .then(res => res.json())
                    .then(data => updateUI(data.present, data.absent))
                    .catch(err => console.error('خطا در دریافت آمار:', err));
            }

            fetchStats();
            setInterval(fetchStats, 5000);

            /* ── Elections (Uncomment to activate) ── */
            /*
            const electionsContainer = document.getElementById('elections-container');

            function fetchElectionStats() {
                fetch(`/events/{{ $event->id }}/live-election-stats`)
                    .then(res => res.json())
                    .then(data => {
                        electionsContainer.innerHTML = data.map(election => `
                    <div class="col-xl-4 mb-4">
                        <div class="d-card">
                            <div style="background: linear-gradient(135deg, #534AB7, #6610f2); padding: 0.85rem 1.25rem; border-radius: 16px 16px 0 0;">
                                <h6 style="color:white; margin:0; font-family:'Vazirmatn',sans-serif;">${election.title}</h6>
                            </div>
                            <div class="d-card-body text-center">
                                <h2 style="color:#534AB7; font-weight:700; font-size:32px;">${election.percent}%</h2>
                                <p style="color:#9ca3af; font-size:13px; margin-bottom:12px;">
                                    ${election.used_stock} سهم از ${election.total_stock} سهم
                                </p>
                                <div class="prog-track">
                                    <div class="prog-fill" style="width:${election.percent}%; background:linear-gradient(90deg,#534AB7,#a78bfa);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
                    })
                    .catch(err => console.error('Election error:', err));
            }

            fetchElectionStats();
            setInterval(fetchElectionStats, 5000);
            */

        });
    </script>
@endsection
