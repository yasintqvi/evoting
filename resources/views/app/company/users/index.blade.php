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
                    <a href="{{route('company.users.create' , $company->slug )}}" class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد کاربران</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>

                            <th><span class="m-3">نام نام خانوادگی</span></th>
                            <th><span class="m-3">تلفن همراه</span></th>
                            <th><span class="m-3">تعداد سهام عادی</span></th>
                            <th><span class="m-3">تعداد سهام ممتاز</span></th>
                            <th><span class="m-3">وضعیت</span></th>
                            <th class="text-center" style="width: 120px;">فعالیت</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($company->users as $user)
                        <tr>

                            <td>
                                <a href="#" class="text-dark fw-medium "><span class="m-3">{{ $user->fullName }}</span></a>
                            </td>
                            <td>
                                <span class="m-3">{{ $user->phone }}</span>
                            </td>
                            <td>{{ $user->pivot->normal_stock_count ?? '-' }}</td>
                            <td>{{ $user->pivot->prefered_stock_count ?? '-' }}</td>
                            <td>
                                @if ($user->is_active == 1)
                                <a href="#" class="badge badge-soft-success"> فعال </a>
                                @else
                                <a href="#" class="badge badge-soft-warning"> غیر فعال </a>
                                @endif
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    <a href="{{ route('company.users.edit', [$company->slug, $user->id]) }}"
                                        class="btn btn-soft-success btn-icon btn-sm rounded-circle">
                                        <i class="ti ti-edit fs-16"></i>
                                    </a>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted">هیچ کاربری وجود ندارد.</td>
                        </tr>
                        @endforelse

                    </tbody>

                    <tfoot class="bg-light-subtle fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">جمع سهام اختصاص داده شده:</td>
                            <td>{{ $company->users->sum('pivot.normal_stock_count') }}</td>
                            <td>{{ $company->users->sum('pivot.prefered_stock_count') }}</td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-end">سهام باقیمانده:</td>
                            <td>{{ $company->normal_stock_count - $company->users->sum('pivot.normal_stock_count') }}</td>
                            <td>{{ $company->prefered_stock_count - $company->users->sum('pivot.prefered_stock_count') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    
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