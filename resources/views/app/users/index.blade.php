@extends('app.layouts.app')

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
                                <input type="checkbox" class="form-check-input" id="customCheck1">
                            </th>
                            <th>عنوان</th>
                            <th>گروه ها</th>
                            <th class="text-center" style="width: 120px;">فعالیت</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($users as $user)
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" class="form-check-input" id="customCheck2">
                            </td>
                            <td>
                                <a href="#" class="text-dark fw-medium">{{ $user->fullName }}</a>
                            </td>
                            <td>
                                @forelse($user->groups as $group)
                                    <span class="badge badge-soft-success">{{ $group->title }}</span>
                                @empty
                                <span class="badge badge-soft-danger">مطلعق به هیچ گروهی نیست</span>
                                @endforelse
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    <a href="javascript:void(0);" class="btn btn-soft-primary btn-icon btn-sm rounded-circle"> <i class="ti ti-eye"></i></a>
                                    <a href="{{ route('users.edit' , $user->id) }}" class="btn btn-soft-success btn-icon btn-sm rounded-circle"> <i class="ti ti-edit fs-16"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-sm rounded-circle"> <i class="ti ti-trash"></i></a>
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