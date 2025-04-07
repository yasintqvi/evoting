@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">لیست حضور غیاب</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>


            <li class="breadcrumb-item active">لیست حاضران</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header border-bottom border-dashed d-flex align-items-center">
                <h4 class="header-title">لیست ثبت حضور و غیاب کاربران و حق وکالت</h4>
            </div>

            <div class="card-body">
                <form action="">
                <div class="table-responsive-sm">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>نام و نام خانوادگی</th>
                                <th>وضعیت حضور کاربر</th>
                                <th>واگذاری وکالت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>حامد بیگدلی</td>
                                <td>
                                    <!-- Switch-->
                                    <div>
                                        <input type="checkbox" id="switch01" checked="" data-switch="success">
                                        <label for="switch01" data-on-label="حاضر" data-off-label="غایب" class="mb-0 d-block"></label>
                                    </div>
                                </td>
                                <td>
                                    <select name="type" onchange="checkElectionType(event)" id="election_type" data-toggle="select2" class="form-select">
                                        <option>یاسین تقوی</option>

                                    </select>
                                </td>

                            </tr>
                        </tbody>
                       
                    </table>
                    <div class="text-start mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">ایجاد</button>
                    </div>
                </div> <!-- end table-responsive-->
            </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div>
</div>
@endsection