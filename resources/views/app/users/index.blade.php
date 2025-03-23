@extends('app.layouts.app')

@section('head-tag')
@endsection


@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">کاربران</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

            <li class="breadcrumb-item"><a href="javascript: void(0);">کاربران</a></li>

            <li class="breadcrumb-item active">همه</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                <h4 class="header-title">لیست کاربران</h4>
                <div>
                    <a href="{{route('users.create')}}" class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد کاربران</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">
                            </th>
                            <th>نام نام خانوادگی</th>
                            <th>تلفن همراه</th>
                            <th>گروه ها</th>
                            <th>وضعیت</th>
                            <th class="text-center" style="width: 120px;">فعالیت</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td class="ps-3">
                            </td>
                            <td>
                                <a href="#" class="text-dark fw-medium">{{ $user->fullName }}</a>
                            </td>
                            <td>
                                <a href="#" class="text-dark fw-medium">{{ $user->phone }}</a>
                            </td>
                            <td>
                                @forelse($user->companies as $company)
                                <span class="badge badge-soft-success">{{ $company->title }}</span>
                                @empty
                                <span class="badge badge-soft-danger">مطلعق به هیچ گروهی نیست</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($user->is_active == 1)
                                <a href="#" class="badge badge-soft-success"> فعال </a>
                                @else
                                <a href="#" class="badge badge-soft-warning"> غیر فعال </a>
                                @endif
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    <a href="{{ route('users.edit' , [$company->slug, $user->id]) }}" class="btn btn-soft-success btn-icon btn-sm rounded-circle"> <i class="ti ti-edit fs-16"></i></a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted">هیچ کاربری وجود ندارد.</td>
                        </tr>
                        @endforelse

                    </tbody>


                    <!-- end tbody -->
                </table><!-- end table -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- include alerts --}}
@include('app.alerts.toastr.success')
@endsection