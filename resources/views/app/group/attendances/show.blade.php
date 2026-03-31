@extends('app.layouts.app')

@section('content')
    <div class=" mt-3 p-1">
        <h3>نمایش نمودار: {{ $event->name }}</h3>
    </div>
    <div class="position-relative overflow-hidden mb-4 rounded-4 shadow-sm"
        style="
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.2);
     ">
        <div class="p-3 text-center text-black">
            <div class="m-2">
                <i class="bi bi-people-fill fs-1"></i>
            </div>
            <h2 class="fw-bold mb-2">{{ $event->title }}</h2>
            <p class="mb-0 fs-5">{{ $event->description }}</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title">نمودار پای ساده</h4>
                    <div dir="ltr">
                        <div style="height: 500px;">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- end card body-->
            </div>
            <!-- end card -->
        </div>

        <div class="col-xl-4">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- هدر با گرادینت -->
                <div class="card-header text-white p-3" style="background: linear-gradient(135deg, #6f42c1, #6610f2);">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>اطلاعات جلسه</h5>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3">
                        <div>
                            <p class="mb-1 text-muted small">تعداد حاضرین</p>
                            <h4 id="present-count" class="fw-bold text-success">{{ $event->present_count }}</h4>
                        </div>
                        <div class="display-4 text-success"><i class="bi bi-person-check"></i></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3">
                        <div>
                            <p class="mb-1 text-muted small">تعداد غایبین</p>
                            <h4 id="absent-count" class="fw-bold text-danger">{{ $event->absent_count }}</h4>
                        </div>
                        <div class="display-4 text-danger"><i class="bi bi-person-x"></i></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-3">
                        <div>
                            <p class="mb-1 text-muted small">تاریخ جلسه</p>
                            <h5 class="fw-bold text-primary">{{ $event->created_at->format('Y/m/d') }}</h5>
                        </div>
                        <div class="display-4 text-primary"><i class="bi bi-calendar3"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4" id="elections-container">
            <!-- باکس‌ها اینجا ساخته میشن -->
        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const presentEl = document.getElementById('present-count');
            const absentEl = document.getElementById('absent-count');


            let attendanceChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['حاضر', 'غایب'],
                    datasets: [{
                        label: 'وضعیت حضور',
                        data: [0, 0],
                        backgroundColor: ['#4caf50', '#f44336']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            const eventId = "{{ $event->slug }}";

            function fetchStats(eventId) {
                fetch(`/events/${eventId}/attendance-stats`)
                    .then(res => res.json())
                    .then(data => {
                        // آپدیت نمودار
                        attendanceChart.data.datasets[0].data = [
                            data.present,
                            data.absent
                        ];
                        attendanceChart.update();

                        // آپدیت کارت‌ها
                        presentEl.textContent = data.present;
                        absentEl.textContent = data.absent;

                    })
                    .catch(err => console.error("خطا: ", err));
            }

            fetchStats(eventId);
            setInterval(() => fetchStats(eventId), 5000);
        });
    </script>
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {

            const eventId = "{{ $event->id }}";
            const container = document.getElementById('elections-container');

            function fetchElectionStats() {

                fetch(`/events/${eventId}/live-election-stats`)
                    .then(res => res.json())
                    .then(data => {

                        container.innerHTML = "";

                        data.forEach(election => {

                            container.innerHTML += `
                        <div class="col-xl-4 mb-4">
                            <div class="card shadow-lg border-0 rounded-4">
                                <div class="card-header text-white"
                                     style="background: linear-gradient(135deg, #198754, #20c997);">
                                    <h6 class="mb-0">${election.title}</h6>
                                </div>

                                <div class="card-body text-center">

                                    <h2 class="fw-bold text-success">
                                        ${election.percent}%
                                    </h2>

                                    <p class="text-muted mb-2">
                                        ${election.used_stock} سهم استفاده شده
                                        از
                                        ${election.total_stock} سهم
                                    </p>

                                    <div class="progress" style="height: 18px;">
                                        <div class="progress-bar bg-success"
                                             role="progressbar"
                                             style="width: ${election.percent}%">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    `;
                        });

                    })
                    .catch(err => console.error("Election error:", err));
            }

            fetchElectionStats();
            setInterval(fetchElectionStats, 5000);

        });
    </script> --}}
@endsection
