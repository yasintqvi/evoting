@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">جزئیات همه پرسی</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

            <li class="breadcrumb-item active">جزئیات</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-6 col-lg-12">
                    <div>
                        <h4 class="card-title">اطلاعات شخصی:</h4>
                        <div class="table-responsive mt-3 border border-dashed rounded px-2 py-1">
                            <table class="table table-borderless m-0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <p class="mb-0">عنوان انتخابات: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['title'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">نوع: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['fa_type'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">وضعیت: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">
                                            <span class="badge badge-soft-success">{{ $election['fa_status'] }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو اصلی: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['main_member_count'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو علی البدل: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14"> {{ $election['substitute_member_count'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو اصلی بازرس: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['incpector_main_member_count'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو علی البدل بازرس: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['incpector_substitute_member_count'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="d-flex mb-0 align-items-center gap-1">تاریخ ایجاد : </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">{{ $election['created_at'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12">
                    <div>
                        <h4 class="card-title">میزان مشارکت ها:</h4>

                        <div dir="ltr" class="mt-5">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                    <!-- end card body-->
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach ($election['rounds'] as $key => $electionRound)
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">دور {{ $key + 1 }} انتخابات</h4>
                <div class="d-flex align-items-center">
                    <a href="{{ route('election-rounds.show', [$company->slug, $election->id, $electionRound->id]) }}" class="btn btn-outline-success mx-2">دیدن نتایج</a>
                    @if ($election['rounds']->where('is_active', true)->exists())
                    <form action="{{ route('voting.terminate', [$company->slug, $election->id]) }}" class="p-0 m-0" method="post">
                        @csrf
                        <button class="btn btn-danger">خاتمه دادن</a>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection

@section('scripts')
<!-- Apex Chart js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['مشارکت', 'عدم مشارکت'],
            datasets: [{
                label: '# میزان مشارکت',
                data: ["{{ $election['participant_percent'] }}", "{{ 100 - $election['participant_percent'] }}"],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 20,
                hoverBorderColor: 'rgba(0, 0, 0, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            family: 'Tahoma'
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 16
                    },
                    bodyFont: {
                        size: 14
                    },
                    footerFont: {
                        size: 12
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart',
                animateRotate: true,
                animateScale: true
            }
        }
    });
</script>



@endsection