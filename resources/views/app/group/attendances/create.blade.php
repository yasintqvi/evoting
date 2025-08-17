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
                    <form action="{{ route('attendances.store', [$group->slug, $event->id]) }}" method="post">
                        @csrf
                        <div class="table-responsive-sm">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>نام و نام خانوادگی</th>
                                        <th>وضعیت حضور</th>
                                        <th>وکالت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->full_name }}</td>
                                            <td>
                                                <input type="hidden" name="attendance[{{ $user->id }}][status]"
                                                    value="0">
                                                <input type="checkbox" name="attendance[{{ $user->id }}][status]"
                                                    id="attendance-{{ $user->id }}" value="1" data-switch="1"
                                                    @checked(old("attendance.{$user->id}.status") == 1)>
                                                <label for="attendance-{{ $user->id }}" data-on-label="حاضر"
                                                    data-off-label="غایب" class="mb-0 d-block"></label>
                                            </td>
                                            <td>
                                                <input type="hidden" name="" value="">
                                                <select name="12" id="" data-toggle="select2"
                                                    class="form-select">
                                                    <option value="">انتخاب وکیل</option>
                                                    <option value="1">
                                                        یاسین تقوی
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="text-start mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">ثبت</button>
                            </div>
                        </div>
                    </form>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div>
    </div>
@endsection
