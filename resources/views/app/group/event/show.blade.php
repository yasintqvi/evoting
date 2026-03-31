@extends('app.layouts.app')

@section('content')
@php
    function getActivityIcon(string $event) {
        return match ($event) {
            'created' => 'ti-plus bg-success-subtle text-success',
            'updated' => 'ti-edit bg-info-subtle text-info',
            'deleted' => 'ti-trash bg-danger-subtle text-danger',
            default => 'ti-eye bg-info-subtle text-info'
        };
    }

    function getActivityTitle(string $event) {
        return match ($event) {
            'created' => "ایجاد",
            "updated" => "ویرایش",
            "deleted" => "حذف",
            default => "دیده شد"
        };
    }
@endphp
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">داشبورد</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index', $group) }}">رویداد ها</a></li>

                <li class="breadcrumb-item active">جزئیات رویداد {{ $event->name }}</li>
            </ol>

        </div>
    </div>
    <div class="row">
        <div class="col-xl-5">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0">ویرایش رویداد</h4>
                </div>

                <form action="{{ route('events.update', [$group, $event]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">نام رویداد</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="نام رویداد را وارد کنید" value="{{ old('name', $event->name) }}">
                                    @error('name')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">عنوان رویداد</label>
                                    <input type="text" class="form-control" name="title" id="title"
                                        placeholder="عنوان را وارد کنید" value="{{ old('title', $event->title) }}">
                                    @error('title')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="quorum_percent" class="form-label">حد نصاب مشارکت (درصد)</label>
                                <input type="number" min="1" max="100" class="form-control" id="quorum_percent"
                                    name="quorum_percent" value="{{ old('quorum_percent', $event->quorum_percent) }}">
                                @error('quorum_percent')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="logo" class="form-label">لوگو (اختیاری)</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                                @error('logo')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror

                                @if ($event->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset($event->logo) }}" alt="لوگو" class="img-thumbnail"
                                            width="120">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div>
                                <label for="description" class="form-label">توضیحات (اختیاری)</label>
                                <textarea class="form-control mt-1" name="description" id="description" rows="3"
                                    placeholder="درباره ی رویداد بنویسید">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">بروزرسانی</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row col-xl-7" style="flex-flow: column;">
            <div class="row  gap-2" style="flex-wrap: nowrap;">
                <div class="card col-xl-4">
                    <div class="card-body">
                        <a href="{{ route('attendances.create', [$group, $event]) }}" target="_blank"
                            class="text-muted float-end mt-n1 fs-18"><i class="ti ti-external-link"></i></a>
                        <h5 class="text-muted fs-13 text-uppercase" title="Number of Orders">افراد حاضر</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ $event->present_count }} / {{ $event->participants->count() }} نفر
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="card col-xl-4">
                    <div class="card-body">
                        <a href="{{ route('elections.index', [$group, $event]) }}" target="_blank"
                            class="text-muted float-end mt-n1 fs-18"><i class="ti ti-external-link"></i></a>
                        <h5 class="text-muted fs-13 text-uppercase" title="Number of Orders">انتخابات</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ $event->elections->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card col-xl-4">
                    <div class="card-body">
                        <a href="{{ route('surveys.index', [$group, $event]) }}" target="_blank"
                            class="text-muted float-end mt-n1 fs-18"><i class="ti ti-external-link"></i></a>
                        <h5 class="text-muted fs-13 text-uppercase" title="Number of Orders">نظر سنجی ها</h5>
                        <div class="d-flex align-items-center gap-2 my-3">
                            <div class="avatar-md flex-shrink-0">
                                <span class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <h3 class="mb-0 fw-bold">{{ $event->surveys->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 p-0">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <h4 class="card-title mb-0"> لیست فعالیت ها</h4>
                    </div>
                    <div class="card-body p-0 border-top border-dashed">
                        <div class="my-3 px-3" data-simplebar="init">
                            <div class="simplebar-wrapper">
                                <div class="simplebar-height-auto-observer-wrapper">
                                    <div class="simplebar-height-auto-observer"></div>
                                </div>
                                <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="left: 0px; bottom: 0px;">
                                        <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                            aria-label="scrollable content"
                                            style="height: auto; overflow: hidden scroll;">
                                            <div class="simplebar-content" style="padding: 0px 24px;">
                                                <div class="timeline-alt py-0">
                                                    @foreach ($activities as $activity)
                                                        <div class="timeline-item">
                                                            <i
                                                                class="ti {{ getActivityIcon($activity->event) }} timeline-icon"></i>
                                                            <div class="timeline-item-info">
                                                                <a href="javascript:void(0);"
                                                                    class="link-reset fw-semibold mb-1 d-block">
                                                                    {{ getActivityTitle($activity->event) }} 
                                                                </a>
                                                                <span class="mb-1">{{ $activity->description }}</span>
                                                                <div class="mb-0 pb-3 flex justify-content-between align-items-center">
                                                                    <small class="text-muted">{{ jalali($activity->created_at)->format("H:i:s Y/m/d")  }} - </small>
                                                                    <small class="text-muted">ایجاد شده توسط {{ $activity?->causer?->full_name  }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <!-- end timeline -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="simplebar-placeholder" style="width: 296px; height: 686px;"></div>
                            </div>
                            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                            </div>
                            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                                <div class="simplebar-scrollbar"
                                    style="height: 199px; transform: translate3d(0px, 0px, 0px); display: block;"></div>
                            </div>
                        </div> <!-- end slimscroll -->
                    </div>
                </div>
            </div>
        </div>






        {{-- <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>اطلاعات جلسه</h5>
                </div>

                <div class="card-body">
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
            </div> --}}
    </div>
    <div class="row mt-4" id="elections-container">
        <!-- باکس‌ها اینجا ساخته میشن -->
    </div>

    </div>
@endsection

@section('scripts')
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
