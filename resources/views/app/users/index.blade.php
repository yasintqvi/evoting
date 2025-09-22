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
                <div class="col-lg-6 gap-2 d-flex">
                    <h4 class="header-title mt-2">لیست کاربران</h4>
                    <form method="GET" action="">
                        <div class="position-relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control ps-4" placeholder="جستجو...">
                            <i class="ti ti-search position-absolute top-50 translate-middle-y ms-2"></i>
                        </div>
                    </form>
                </div>
                <div>
                    <a href="{{ route('users.create') }}" class="btn btn-success bg-gradient"><i
                            class="ti ti-plus me-1"></i>ایجاد کاربران</a>
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
                                @if ($user->is_active == 1)
                                <a href="#" class="badge badge-soft-success"> فعال </a>
                                @else
                                <a href="#" class="badge badge-soft-warning"> غیر فعال </a>
                                @endif
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    @can(\App\Enums\Permission::CHANGE_ACCESS->value)
                                    <a href="{{ route('users.change-access.edit', $user->id) }}"
                                        class="btn btn-soft-primary btn-sm">تغییر دسترسی</a>
                                    @endcan
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="btn btn-secondary btn-sm">
                                        <i class="ti ti-edit"></i></a>
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