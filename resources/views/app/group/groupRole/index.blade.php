@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">مدیریت نقش‌ها</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
            <li class="breadcrumb-item active">نقش‌ها</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                <h4 class="header-title">لیست نقش‌ها</h4>
                <div>
                    <a href="{{ route('group.permissions.create',[$group]) }}" class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد نقش جدید</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>نام نقش</th>
                            <th>تاریخ ایجاد</th>
                            <th class="text-center" style="width: 120px;">اقدامات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ verta($role->created_at)->format('Y/m/d H:i') }}</td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    <a href="{{ route('group.permissions.edit', [$group,$role]) }}" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i></a>
                                    @if(!in_array($role->name, [App\Enums\Role::Manager->value]))
                                    <form action="{{ route('group.permissions.destroy', [$group,$role]) }}" method="POST" class="d-inline delete-role-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm delete-role-btn">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted">هیچ نقشی یافت نشد</td>
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
