@extends('app.layouts.app')

@section('title', 'ویرایش نقش')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <h4 class="card-title">فرم ویرایش نقش</h4>
                <p class="text-muted mb-0">لطفا اطلاعات نقش را ویرایش کنید</p>
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

                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">نام نقش</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
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
                                            {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || 
                                               (!old() && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary">بروزرسانی</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">انصراف</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection