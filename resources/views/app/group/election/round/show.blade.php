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

<div class="container mt-4">
    <!-- نمودار هیئت مدیره -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">نتایج رای‌های هیئت مدیره</h5>
        </div>
        <div class="card-body">
            <canvas id="directorChart" height="500"></canvas>
        </div>
    </div>

    <!-- نمودار بازرس -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">نتایج رای‌های بازرس</h5>
        </div>
        <div class="card-body">
            <canvas id="inspectorChart" height="500"></canvas>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const directorCtx = document.getElementById('directorChart').getContext('2d');

        const directorGradient = directorCtx.createLinearGradient(0, 0, 0, 400);
        directorGradient.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
        directorGradient.addColorStop(1, 'rgba(75, 192, 192, 0.6)');

        const directorChart = new Chart(directorCtx, {
            type: 'bar',
            data: {
                labels: @json($directorCandidates),
                datasets: [{
                    label: 'تعداد رای‌ها',
                    data: @json($directorVoteCounts),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
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
                            text: 'تعداد رای‌ها',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'نامزدها',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });

        const inspectorCtx = document.getElementById('inspectorChart').getContext('2d');

        const inspectorGradient = inspectorCtx.createLinearGradient(0, 0, 0, 400);
        inspectorGradient.addColorStop(0, 'rgba(255, 99, 132, 0.6)');
        inspectorGradient.addColorStop(1, 'rgba(255, 159, 64, 0.6)');

        const inspectorChart = new Chart(inspectorCtx, {
            type: 'bar',
            data: {
                labels: @json($inspectorCandidates),
                datasets: [{
                    label: 'تعداد رای‌ها',
                    data: @json($inspectorVoteCounts), // تعداد رای‌ها
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
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
                            text: 'تعداد رای‌ها',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'نامزدها',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart'
                }
            }
        });
    });
</script>
@endsection