@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">نتایج انتخابات {{ $election->title }}</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>
            <li class="breadcrumb-item active">نتایج انتخابات {{ $election->title }} </li>
        </ol>
    </div>
</div>

<div class="card">
    <div class="d-flex">
        <div class="offcanvas-xl offcanvas-start file-manager" tabindex="-1" id="fileManagerSidebar" aria-labelledby="fileManagerSidebarLabel">
            <!-- users -->
            <div class="d-flex flex-column">
                <div class="py-2 px-3 flex-shrink-0 d-flex align-items-center gap-2 border-bottom border-dashed">
                    <!-- user -->
                    <div class="avatar-md">
                        <img src="assets/images/users/avatar-1.jpg" alt="" class="img-fluid rounded-circle">
                    </div>
                    <div>
                        <h5 class="mb-1 fs-16 fw-semibold">نتایج آرای {{ $election->title }}
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-soft-danger ms-auto d-xl-none" data-bs-dismiss="offcanvas" data-bs-target="#fileManagerSidebar" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="p-3">
                    <div class="d-flex flex-column">
                        <a type="button" href="{{ route('elections.show', [$group->slug, $election->id]) }}" class="btn fw-medium btn-success drop-arrow-none dropdown-toggle w-100 mb-3">
                            بازگشت به جزئیات
                        </a>
                        <div class="file-menu">
                            <div class="file-menu">
                                <a id="toggleChartResults" class="list-group-item active" style="cursor: pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clipboard-data" viewBox="0 0 16 16">
                                        <path d="M4 11a1 1 0 1 1 2 0v1a1 1 0 1 1-2 0zm6-4a1 1 0 1 1 2 0v5a1 1 0 1 1-2 0zM7 9a1 1 0 0 1 2 0v3a1 1 0 1 1-2 0z" />
                                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z" />
                                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z" />
                                    </svg> نتایج به صورت نموداری
                                </a>
                            </div>
                            <div class="file-menu">
                                <a id="toggleTableResultsSidebar" class="list-group-item" style="cursor: pointer">
                                    <i class="ti ti-table fs-18 align-middle me-2"></i>نتایج به صورت جدولی
                                </a>
                            </div>


                        </div>


                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 border-start">
            <div class="p-3">
                <div class="d-flex align-items-center gap-1 ">
                    <div class="flex-shrink-0 d-xl-none d-inline-flex">
                        <button class="btn btn-sm btn-icon btn-soft-primary align-items-center p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#fileManagerSidebar" aria-controls="fileManagerSidebar">
                            <i class="ti ti-menu-2 fs-20"></i>
                        </button>
                    </div>
                    <h4 class="header-title">نتایج انتخابات</h4>
                </div>

                <div class="row">
                    <div id="cardShow" class="container mt-4">
                        <!-- نمودار هیئت مدیره -->
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="card-title mb-0">نتایج رای‌های هیئت مدیره</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="directorChart" height="500"></canvas>
                            </div>
                        </div>

                        <!-- نمودار بازرس -->
                        <div class="card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">نتایج رای‌های بازرس</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="inspectorChart" height="500"></canvas>
                            </div>
                        </div>
                    </div>


                    <div id="tableResults" class="row mt-4" style="display: none;">
                        <div class="col-lg-12">
                            <!-- لیست رأی‌ها کاندیدهای اصلی -->
                            <div class="card mt-2">
                                <div class="card-header border-bottom border-dashed">
                                    <h4 class="card-title mb-0">لیست رأی‌ها کاندید های اصلی</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap mb-0">
                                            <thead class="bg-light bg-opacity-25">
                                                <tr>
                                                    <th class="ps-3" style="width: 50px;">#</th>
                                                    <th>نام کاندید</th>
                                                    <th>تعداد رأی</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($directorCandidates as $index => $candidate)
                                                <tr>
                                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                                    <td>{{ $candidate['name'] }}</td>
                                                    <td>{{ $candidate['votes'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- لیست رأی‌ها بازرس -->
                            <div class="card mt-2">
                                <div class="card-header border-bottom border-dashed">
                                    <h4 class="card-title mb-0">لیست رأی‌ها بازرس</h4>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-nowrap mb-0">
                                            <thead class="bg-light bg-opacity-25">
                                                <tr>
                                                    <th class="ps-3" style="width: 50px;">#</th>
                                                    <th>نام کاندید</th>
                                                    <th>تعداد رأی</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($inspectorCandidates as $index => $candidate)
                                                <tr>
                                                    <td class="ps-3">{{ $loop->iteration }}</td>
                                                    <td>{{ $candidate['name'] }}</td>
                                                    <td>{{ $candidate['votes'] }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    @endsection

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const directorCtx = document.getElementById('directorChart').getContext('2d');

            const directorLabels = @json(collect($directorCandidates) -> pluck('name'));
            const directorVotes = @json(collect($directorCandidates) -> pluck('votes'));

            const directorChart = new Chart(directorCtx, {
                type: 'bar',
                data: {
                    labels: directorLabels,
                    datasets: [{
                        label: 'تعداد رای‌ها',
                        data: directorVotes,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'تعداد رای‌ها'
                            }
                        }
                    }
                }
            });

            const inspectorCtx = document.getElementById('inspectorChart').getContext('2d');

            // استخراج نام‌ها و تعداد رأی‌های بازرسان
            const inspectorLabels = @json(collect($inspectorCandidates) -> pluck('name'));
            const inspectorVotes = @json(collect($inspectorCandidates) -> pluck('votes'));

            const inspectorChart = new Chart(inspectorCtx, {
                type: 'bar',
                data: {
                    labels: inspectorLabels,
                    datasets: [{
                        label: 'تعداد رای‌ها',
                        data: inspectorVotes,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'تعداد رای‌ها'
                            }
                        }
                    }
                }
            });
        });
    </script>





    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableResults = document.getElementById('tableResults');
            const cardShow = document.getElementById('cardShow');
            const toggleChartBtn = document.getElementById('toggleChartResults');
            const toggleTableBtn = document.getElementById('toggleTableResultsSidebar');

            toggleChartBtn.addEventListener('click', function() {
                this.classList.add('active');
                toggleTableBtn.classList.remove('active');

                cardShow.style.display = 'block';
                tableResults.style.display = 'none';
            });

            toggleTableBtn.addEventListener('click', function() {
                this.classList.add('active');
                toggleChartBtn.classList.remove('active');

                cardShow.style.display = 'none';
                tableResults.style.display = 'block';
            });
        });
    </script>

    @endsection