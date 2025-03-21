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
                                        <td class="px-2 text-dark fw-medium fs-14">انتخابات هیئت مدیره ۱۴۰۳-۱۴۰۴</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">نوع: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">تک انتخابی</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">وضعیت: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">
                                            <span class="badge badge-soft-success">در حال برگزاری</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو اصلی: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">۲</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو علی البدل: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14"> ۱</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو اصلی بازرس: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">۲</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="mb-0">تعداد عضو علی البدل بازرس: </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">۴</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p class="d-flex mb-0 align-items-center gap-1">تاریخ ایجاد : </p>
                                        </td>
                                        <td class="px-2 text-dark fw-medium fs-14">۱۴۰۳-۰۴-۱</td>
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
                            <div id="simple-pie" class="apex-charts" data-colors="#0acf97,#ccc"></div>
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
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title mb-0">دوره های انتخاباتی</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="bg-light bg-opacity-25">
                            <tr>
                                <th class="ps-3" style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="customCheck1">
                                </th>
                                <th>دور انتخاباتی</th>
                                <th>میزان مشارکت</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th class="text-center" style="width: 125px;">فعالیت</th>
                            </tr>
                        </thead><!-- end thead -->
                        <tbody>
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" class="form-check-input" id="customCheck2">
                                </td>
                                <td>
                                    ۱
                                </td>
                                <td>۴۵٪</td>
                                <td>
                                    9:00 صبح
                                </td>
                                <td>
                                    12:00 عضر
                                </td>
                                <td class="pe-3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary">دیدن نتایج</button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox" class="form-check-input" id="customCheck3">
                                </td>
                                <td>
                                    ۲
                                </td>
                                <td>۲۸٪</td>
                                <td>
                                    ۱۵:۰۰ عصر
                                </td>
                                <td>
                                    در حال برگزاری
                                </td>
                                <td class="pe-3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-secondary">دیدن نتایج</button>
                                        <button type="button" class="btn btn-success">شرکت در همه پرسی</button>
                                    </div>
                                </td>
                            </tr>

                        </tbody><!-- end tbody -->
                    </table><!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('app.alerts.toastr.success')
@endsection