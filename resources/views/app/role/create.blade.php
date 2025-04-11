@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ایجاد نقش جدید</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">نقش‌ها</a></li>
            <li class="breadcrumb-item active">ایجاد</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <h4 class="card-title">فرم ایجاد نقش</h4>
                <p class="text-muted mb-0">لطفا اطلاعات نقش جدید را وارد کنید</p>
            </div>

            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">نام نقش</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>دسترسی‌ها</h5>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input @error('permissions') is-invalid @enderror" id="permission_{{ $permission->id }}"
                                            name="permissions[]" value="{{ $permission->id }}"
                                            {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ \App\Enums\Permission::from($permission->name)->fa() }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('permissions')
                            <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">ذخیره</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">انصراف</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('app.alerts.toastr.error')
@endsection