@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0"> کاربران گروه: {{ $group->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">داشبورد</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.users.index', $group) }}">کاربران</a></li>
                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    {{-- نمایش اطلاعات کلی سهام برای گروه سهامی خاص --}}
    @if ($group->type == App\Enums\GroupType::SPECIAL)
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    کل سهام عادی گروه</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($group->normal_stock_count) }} سهم
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-chart-bar fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    کل سهام ممتاز گروه</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($group->prefered_stock_count) }} سهم
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-chart-pie fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    وزن سهام گروه</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($group->prefered_stock_weight) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="ti ti-scale fs-2 text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- نمایش سهام تخصیص داده شده و باقیمانده --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    سهام عادی تخصیص داده شده
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($allocatedNormalStock) }}
                                    از {{ number_format($group->normal_stock_count) }} سهم
                                </div>
                                <div class="progress mt-2"
                                    style="height: 25px; background-color: #e9ecef; border-radius: 10px; position: relative;">
                                    @php
                                        $normalPercentage =
                                            $group->normal_stock_count > 0
                                                ? ($allocatedNormalStock / $group->normal_stock_count) * 100
                                                : 0;
                                    @endphp
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $normalPercentage }}%; border-radius: 10px; position: relative;">
                                        @if ($normalPercentage > 15)
                                            <span
                                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: white; font-size: 12px; font-weight: bold;">
                                                {{ number_format($normalPercentage, 1) }}%
                                            </span>
                                        @endif
                                    </div>
                                    @if ($normalPercentage <= 15)
                                        <span
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #333; font-size: 12px; font-weight: bold;">
                                            {{ number_format($normalPercentage, 1) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    سهام ممتاز تخصیص داده شده
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($allocatedPreferedStock) }}
                                    از {{ number_format($group->prefered_stock_count) }} سهم
                                </div>
                                <div class="progress mt-2"
                                    style="height: 25px; background-color: #e9ecef; border-radius: 10px; position: relative;">
                                    @php
                                        $preferedPercentage =
                                            $group->prefered_stock_count > 0
                                                ? ($allocatedPreferedStock / $group->prefered_stock_count) * 100
                                                : 0;
                                    @endphp
                                    <div class="progress-bar bg-danger"
                                        style="width: {{ $preferedPercentage }}%; border-radius: 10px; position: relative;">
                                        @if ($preferedPercentage > 15)
                                            <span
                                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: white; font-size: 12px; font-weight: bold;">
                                                {{ number_format($preferedPercentage, 1) }}%
                                            </span>
                                        @endif
                                    </div>
                                    @if ($preferedPercentage <= 15)
                                        <span
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #333; font-size: 12px; font-weight: bold;">
                                            {{ number_format($preferedPercentage, 1) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row ">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-light">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <form class="col-lg-8 gap-2 d-flex" method="get" action="">
                            <h4 class="header-title mt-2">لیست کاربران</h4>
                            <div class="position-relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control ps-4" placeholder="جستجو...">
                                <i class="ti ti-search position-absolute top-50 translate-middle-y ms-2"></i>
                            </div>
                            <div class="position-relative">
                                <select class="form-control" name="status">
                                    <option value="">همه</option>
                                    <option value="1" @selected(request('status') == 1)>فعال</option>
                                    <option value="2" @selected(request('status') == 2)>غیر فعال</option>
                                </select>
                            </div>

                            <button class=" btn btn-primary bg-gradient">جستجو</button>
                            <a href="{{ route('group.users.index', $group) }}" class="btn btn-danger bg-gradient">حذف
                                فیلتر</a>
                        </form>
                        @can(\App\Enums\Permission::CREATE_GROUP_USERS->value)
                            <a href="{{ route('group.users.create', $group) }}" class="btn btn-success bg-gradient h-100 p-2">
                                <i class="ti ti-plus me-1"></i>کاربر جدید
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th><span class="m-3">نام نام خانوادگی</span></th>
                                <th><span class="m-3">تلفن همراه</span></th>
                                @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
                                    <th><span class="m-3">تعداد سهام عادی</span></th>
                                    <th><span class="m-3">تعداد سهام ممتاز</span></th>
                                @endif
                                <th><span class="m-3">وضعیت</span></th>
                                <th class="text-center" style="width: 120px;">فعالیت</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <a href="#" class="text-dark fw-medium">
                                            <span class="m-3">{{ $user->fullName }}</span>
                                        </a>
                                    </td>
                                    <td><span class="m-3">{{ $user->phone }}</span></td>
                                    @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
                                        <td>{{ number_format($user->pivot->normal_stock_count ?? 0) }}</td>
                                        <td>{{ number_format($user->pivot->prefered_stock_count ?? 0) }}</td>
                                    @endif
                                    <td>
                                        @if ($user->is_active == 1)
                                            <span class="badge badge-soft-success p-1">فعال</span>
                                        @else
                                            <span class="badge badge-soft-danger p-1">غیر فعال</span>
                                        @endif
                                    </td>

                                    <td class="pe-3">
                                        <div class="hstack gap-1 justify-content-end">
                                            @can(\App\Enums\Permission::EDIT_GROUP_USERS->value)
                                                <a href="{{ route('group.users.edit', [$group, $user]) }}"
                                                    class="btn btn-secondary btn-sm">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                            @endcan
                                            @can(\App\Enums\Permission::MANAGE_GROUP_USER_ACCESS->value)
                                                <a href="{{ route('users.change-access.edit', $user->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="ti ti-key"></i>
                                                </a>
                                            @endcan
                                            @can(\App\Enums\Permission::DELETE_GROUP_USERS->value)
                                                <form action="{{ route('group.users.destroy', [$group, $user]) }}"
                                                    method="POST" class="d-inline delete-role-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-role-btn">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">هیچ کاربری وجود ندارد.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.querySelectorAll('.delete-role-btn').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-role-form');

                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: "این عملیات غیرقابل برگشت است!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'بله، حذف شود',
                    cancelButtonText: 'انصراف'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
