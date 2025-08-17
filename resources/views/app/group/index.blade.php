@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">{{ $group->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

                <li class="breadcrumb-item active">{{ $group->title }}</li>
            </ol>
        </div>
    </div>

    <div class="d-flex flex-row gap-3 p-3">

        <!-- مرحله 1 -->
        <div class="d-flex flex-column flex-fill p-3 rounded shadow-sm border-start border-4 border-success ">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-success text-white rounded px-3 py-2 fw-bold">✓</div>
                <div class="text-success fw-semibold">ایجاد گروه</div>
            </div>
            <small class="text-success align-self-end">تکمیل شده</small>
        </div>

        <!-- مرحله 2 -->
        @can(\App\Enums\Permission::VIEW_GROUP_USERS->value)
            @php
                $isApproved = $usersCount >= 3;
            @endphp

            <div
                class="d-flex flex-column flex-fill p-3 rounded shadow-sm border-start border-4
        {{ $isApproved ? 'border-success' : 'border-primary bg-white' }}
        transition-all hover:shadow-lg">

                <a href="{{ route('group.users.index', $group->slug) }}"
                    class="text-decoration-none {{ $isApproved ? 'text-success' : 'text-primary' }}">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div
                            class="{{ $isApproved ? 'bg-success text-white' : 'bg-secondary text-white' }} rounded px-3 py-2 fw-bold">
                            {{ $isApproved ? '✓' : '2' }}
                        </div>
                        <div class="{{ $isApproved ? 'text-success fw-semibold' : 'text-primary fw-semibold' }}">
                            {{ $isApproved ? 'تایید شده - اعضا اضافه شدند' : 'ایجاد اعضا' }}
                        </div>
                    </div>
                </a>

                <small class="{{ $isApproved ? 'text-success' : 'text-primary' }} align-self-end">
                    {{ $isApproved ? 'تکمیل شده' : 'در حال انتظار' }}
                </small>
            </div>
        @endcan

        <!-- مرحله 3 -->
        @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
            <div class="d-flex flex-column flex-fill p-3 rounded shadow-sm border-start border-4 border-secondary bg-white">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="bg-secondary text-white rounded px-3 py-2 fw-bold">3</div>
                    <div class="text-muted fw-semibold">ایجاد سهام‌داران</div>
                </div>
                <small class="text-muted align-self-end">در انتظار</small>
            </div>
        @endif
    </div>

    <h4>{{ $group->title }}</h4>


    @foreach ($events as $event)
        <a href="#" class="event-link" data-id="{{ $event->id }}">
            {{ $event->title }}
        </a><br>
    @endforeach

    <canvas id="attendanceChart" style="max-height: 300px; width: 300%;"></canvas>
@endsection



@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
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

            let currentEventId = null;

            document.querySelectorAll('.event-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    currentEventId = this.dataset.id;
                    fetchStats(currentEventId); 
                });
            });

            function fetchStats(eventId) {
                if (!eventId) return;

                console.log("در حال ارسال درخواست برای رویداد: ", eventId);

                fetch(`/events/${eventId}/attendance-stats`)
                    .then(res => {
                        if (!res.ok) throw new Error("خطا در دریافت دیتا");
                        return res.json();
                    })
                    .then(data => {
                        console.log("داده دریافت شد: ", data);

                        attendanceChart.data.datasets[0].data = [
                            data.present,
                            data.absent,
                        ];
                        attendanceChart.update();
                    })
                    .catch(err => {
                        console.error("خطا: ", err);
                    });
            }

            setInterval(() => {
                if (currentEventId) {
                    fetchStats(currentEventId);
                }
            }, 5000);
        });
    </script>
@endsection
