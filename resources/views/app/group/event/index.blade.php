@extends('app.layouts.app')
@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">لیست رویداد</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{route('events.index',$group)}}">داشبورد</a></li>

                <li class="breadcrumb-item"><a href="">رویداد ها</a></li>

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
                            <h4 class="header-title mt-2">لیست رویداد ها</h4>
                            <div class="position-relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       class="form-control ps-4" placeholder="جستجو...">
                                <i class="ti ti-search position-absolute top-50 translate-middle-y ms-2"></i>
                            </div>

                            <div class="position-relative">
                                <select class="form-control" name="status">

                                    <option value="">همه</option>
                                    <option value="1" @selected(request('status')==1)>ایجاد شده</option>
                                    <option value="2" @selected(request('status')==2)>درحال اجرا</option>
                                    <option value="3" @selected(request('status')==3)>به اتمام رسیده</option>
                                </select>
                            </div>

                            <button class=" btn btn-primary bg-gradient">جست و جو
                            </button>
                            <a href="{{route('events.index',$group)}}" class="btn btn-danger bg-gradient">حذف فیلتر </a>

                        </form>


                        <a href="{{ route('events.create', $group) }}"
                           class="btn btn-success bg-gradient h-100 p-2">
                            <i class="ti ti-plus me-1"></i>افزودن رویداد
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap mb-0 align-items-center text-center">
                        <thead class="bg-light-subtle">
                        <tr>
                            <th><span class="m-3">شناسه</span></th>
                            <th><span class="m-3">لوگو</span></th>
                            <th><span class="m-3">نام رویداد</span></th>
                            <th><span class="m-3">عنوان رویداد</span></th>
                            <th><span class="m-3">حد نصاب مشارکت</span></th>
                            <th><span class="m-3">وضعیت</span></th>
                            <th><span class="m-3">عملیات</span></th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse ($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td>@if($event->logo)
                                        <a target="_blank" href="{{ asset( $event->logo) }}">
                                            <img
                                                src="{{ asset($event->logo) }}"
                                                alt="لوگوی رویداد"
                                                class="img-thumbnail rounded-circle"
                                                width="50" height="50"
                                            >
                                        </a>
                                    @else
                                        <span class="text-muted">بدون لوگو</span>
                                    @endif</td>
                                <td>
                                    <a href="#" class="text-dark fw-medium">{{ $event->name }}</a>
                                </td>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->quorum_percent ?? '-' }}</td>
                                <td>
                                    @if ($event->status == App\Enums\EventStatus::Created)
                                        <a href="#" class="badge badge-soft-warning p-1">ایجاد شده</a>
                                    @elseif($event->status == App\Enums\EventStatus::Finished)
                                        <a href="#" class="badge badge-soft-danger p-1">به اتمام رسیده</a>
                                    @else
                                        <a href="#" class="badge badge-soft-success p-1">درحال اجرا</a>
                                    @endif
                                </td>
                                <td>
                                    <div class="hstack gap-1">
                                        <!-- دکمه ساده (مثلاً ویرایش) -->
                                        <a href="{{ route('events.edit', [$group, $event]) }}"
                                           class="btn btn-secondary btn-sm">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <!-- دکمه منوی کشویی -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('attendances.create', [$group, $event]) }}">
                                                        حضور غیاب
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('elections.index', [$group, $event]) }}" >
                                                       انتخابات
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('surveys.index', [$group, $event]) }}">
                                                       نظرسنجی
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                       href=>
                                                    کلون گیری
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted text-center">هیچ رویدادی وجود ندارد.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        @endsection

        @section('scripts')
            <script>
                document.querySelectorAll('.delete-role-btn').forEach(button => {
                    button.addEventListener('click', function () {
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
