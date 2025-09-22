@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ویرایش کاربر</h4>
        </div>
    </div>

    <div class="card col-lg-6">
        <form id="edit-user-form" action="{{ route('users.update', $user->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label for="first_name" class="form-label">نام</label>
                        <input type="text" name="first_name" class="form-control" id="first_name"
                            value="{{ old('first_name', $user->first_name) }}">
                        @error('first_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="last_name" class="form-label">نام خانوادگی</label>
                        <input type="text" name="last_name" class="form-control" id="last_name"
                            value="{{ old('last_name', $user->last_name) }}">
                        @error('last_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="phone" class="form-label">تلفن همراه</label>
                        <input type="text" name="phone" class="form-control" id="phone"
                            value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="nationalcode" class="form-label">کد ملی</label>
                        <input type="text" name="nationalcode" class="form-control" id="nationalcode"
                            placeholder="کد ملی را وارد کنید" value="{{ old('nationalcode', $user->nationalcode) }}">
                        @error('nationalcode')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="is_active" class="form-label d-block">وضعیت</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                @checked(old('is_active', $user->is_active))>
                            <label class="form-check-label" for="is_active">فعال / غیر فعال</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">ویرایش</button>
            </div>
        </form>
    </div>
@endsection
