@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0"> کاربران گروه: {{ $group->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

                <li class="breadcrumb-item"><a href="javascript: void(0);">کاربران</a></li>

                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

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
                                <option value="1" @selected(request('status')==1)>فعال</option>
                                <option value="2" @selected(request('status')==2)>غیر فعال</option>
                            </select>
                        </div>

                        <button class=" btn btn-primary bg-gradient">جست و جو
                        </button>
                        <a href="{{route('group.users.index',$group)}}" class="btn btn-danger bg-gradient">حذف فیلتر </a>

                    </form>


                        <a href="{{ route('group.users.create', $group) }}"
                            class="btn btn-success bg-gradient h-100 p-2">
                            <i class="ti ti-plus me-1"></i>افزودن کاربر به این گروه
                        </a>
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
                        </thead><!-- end thead -->

                        <tbody>
                            @forelse ($group->users as $user)
                                <tr>

                                    <td>
                                        <a href="#" class="text-dark fw-medium "><span
                                                class="m-3">{{ $user->fullName }}</span></a>
                                    </td>
                                    <td>
                                        <span class="m-3">{{ $user->phone }}</span>
                                    </td>
                                    @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
                                        <td>{{ $user->pivot->normal_stock_count ?? '-' }}</td>
                                        <td>{{ $user->pivot->prefered_stock_count ?? '-' }}</td>
                                    @endif
                                    <td>
                                        @if ($user->is_active == 1)
                                            <a href="#" class="badge badge-soft-success p-1"> فعال </a>
                                        @else
                                            <a href="#" class="badge badge-soft-danger p-1"> غیر فعال </a>
                                        @endif
                                    </td>

                                    <td class="pe-3">
                                        <div class="hstack gap-1 justify-content-end">
                                            <a href="{{ route('group.users.edit', [$group, $user]) }}"
                                                class="btn btn-secondary btn-sm">
                                                <i class="ti ti-edit"></i></a>
                                            <form action="{{ route('group.users.destroy', [$group, $user]) }}"
                                                method="POST" class="d-inline delete-role-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-role-btn">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>

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
        @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-block">
                                <h4 class="card-title mb-0">جزئیات سهام داران</h4>
                            </div>
                            <div class="ms-auto">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card bg-light px-3 py-1 border-0 shadow-sm mb-0" style="min-width: 240px;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-chart-dots text-primary fs-5"></i>
                                    <span class="fw-bold text-primary">سهام اختصاص داده‌شده</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>عادی:</span>
                                    <strong>{{ $group->users->sum('pivot.normal_stock_count') }}</strong>
                                    <span>ممتاز:</span>
                                    <strong>{{ $group->users->sum('pivot.prefered_stock_count') }}</strong>
                                </div>
                            </div>

                            <div class="card bg-light px-3 py-1 border-0 shadow-sm mb-0 mt-3" style="min-width: 240px;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-box-multiple text-warning fs-5"></i>
                                    <span class="fw-bold text-warning">سهام باقیمانده</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>عادی:</span>
                                    <strong>{{ $group->normal_stock_count - $group->users->sum('pivot.normal_stock_count') }}</strong>
                                    <span>ممتاز:</span>
                                    <strong>{{ $group->prefered_stock_count - $group->users->sum('pivot.prefered_stock_count') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        @endif
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
