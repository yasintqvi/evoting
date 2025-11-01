@extends('app.layouts.app')

@section('content')
    <div class="container py-5">
        <h1 class="text-center mb-5 fw-bold">
            آمار نظرسنجی: {{ $survey->title }}
        </h1>

        {{-- وارد کردن Bootstrap و Chart.js --}}
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <div class="accordion" id="surveyAccordion">
            @foreach ($stats as $questionId => $items)
                @php
                    $questionTitle = $items->first()->question_title;
                    $labels = $items->pluck('option_title')->map(fn($o) => $o ?? 'پاسخ متنی');
                    $values = $items->pluck('count');
                    $percents = $items->pluck('percent');
                @endphp

                <div class="accordion-item mb-3 shadow-sm rounded-3 border-0">
                    <h2 class="accordion-header" id="heading{{ $questionId }}">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $questionId }}" aria-expanded="false"
                            aria-controls="collapse{{ $questionId }}">
                            {{ $questionTitle }}
                        </button>
                    </h2>

                    <div id="collapse{{ $questionId }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $questionId }}" data-bs-parent="#surveyAccordion">
                        <div class="accordion-body bg-light">
                            <div class="p-3 bg-white rounded-3 shadow-sm">
                                <canvas id="chart_{{ $questionId }}" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const chartId = 'chart_{{ $questionId }}';
                        const canvas = document.getElementById(chartId);

                        const collapse = document.getElementById('collapse{{ $questionId }}');
                        collapse.addEventListener('shown.bs.collapse', function() {
                            if (!canvas.dataset.initialized) {
                                new Chart(canvas, {
                                    type: 'bar',
                                    data: {
                                        labels: {!! json_encode($labels) !!},
                                        datasets: [{
                                            label: 'تعداد پاسخ‌ها',
                                            data: {!! json_encode($values) !!},
                                            backgroundColor: [
                                                '#0d6efd', '#198754', '#ffc107', '#dc3545',
                                                '#6610f2', '#fd7e14'
                                            ],
                                            borderRadius: 8
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        scales: {
                                            y: {
                                                beginAtZero: true
                                            }
                                        },
                                        plugins: {
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        const percent = {!! json_encode($percents) !!}[context
                                                            .dataIndex];
                                                        return context.dataset.label + ': ' + context
                                                            .formattedValue +
                                                            ' (' + percent + '%)';
                                                    }
                                                }
                                            },
                                            legend: {
                                                display: false
                                            }
                                        }
                                    }
                                });
                                canvas.dataset.initialized = true;
                            }
                        });
                    });
                </script>
            @endforeach
        </div>
    </div>
@endsection
