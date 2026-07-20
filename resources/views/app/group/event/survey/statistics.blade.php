@extends('app.layouts.app')

@section('content')
    <div class="container py-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <h1 class="text-center text-md-start mb-0 fw-bold flex-grow-1">
                آمار نظرسنجی: {{ $survey->title }}
            </h1>
            <div class="d-flex gap-2 shrink-0">
                <a href="{{ route('surveys.statistics', [$group, $event, $survey]) }}?download_pdf=1"
                    class="btn btn-danger bg-gradient" target="_blank" rel="noopener">
                    <i class="ti ti-file-type-pdf me-1"></i>
                    دانلود PDF
                </a>
                <a href="{{ route('surveys.statistics', [$group, $event, $survey]) }}?download_pdf=1&compact=1"
                    class="btn btn-outline-danger" target="_blank" rel="noopener">
                    <i class="ti ti-file-type-pdf me-1"></i>
                    دانلود PDF فشرده
                </a>
            </div>
        </div>

        @if ($isWeighted ?? false)
            <div class="alert alert-info small mb-4">
                آمار بر اساس مجموع سهام عادی و ممتاز هر شرکت‌کننده در این رویداد وزن‌دهی شده است.
            </div>
        @endif

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
