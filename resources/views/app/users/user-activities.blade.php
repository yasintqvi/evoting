@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">لاگ فعالیت کاربران</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
            <li class="breadcrumb-item active">لاگ فعالیت کاربران</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <div class="row justify-content-between gy-2 position-relative">
                    <div class="col-lg-3">
                        <h4 class="header-title">لیست فعالیت های کاربران</h4>
                    </div>

                    <div class="col-sm-8 col-xl-8 col-xxl-4">
                        <form>
                            <div class="d-flex flex-wrap flex-lg-nowrap gap-2">
                                <div class="flex-grow-1">
                                    <select name="user_id" class="form-select my-1 my-md-0 me-sm-3" data-toggle="select2" id="select">
                                        <option value="">یک کاربر را انتخاب کنید</option>
                                        @foreach($users as $user)
                                        <option @selected(request('user_id')==$user->id) value="{{ $user->id }}">{{ $user->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-primary">فیلتر</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>عامل</th>
                            <th>توضیحات</th>
                            <th>نوع رویداد</th>
                            <th>موضوع</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->causer->full_name }}</td>
                            <td>{{ __($activity->event) }}</td>
                            <td>
                                @if($activity->subject)
                                {{ $activity->subject->name ?? $activity->subject->title ?? $activity->subject->name ?? $activity->subject->full_name ?? 'ID: ' . $activity->subject_id }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ verta($activity->created_at)->format('Y/m/d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted">هیچ فعالیتی یافت نشد</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $activities->links('vendor.pagination.bootstrap-5') }}
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