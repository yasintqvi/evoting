@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ویرایش کاربر</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">کاربران</a></li>
                <li class="breadcrumb-item active">ویرایش</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="card col-lg-6">
            <!-- بخش بالای فرم -->
            <div class="d-flex col-lg-12 align-items-center">
                <div class="card-header border-bottom border-dashed col-lg-6">
                    <h4 class="card-title">اطلاعات مربوط به کاربر</h4>
                    <p class="text-muted mb-0">شما در حال ویرایش اطلاعات کاربر هستید</p>
                </div>
            </div>

            <!-- فرم ویرایش کاربر -->
            <form id="edit-user-form" action="{{ route('users.update', $user->id) }}" method="post"
                style="display: block;">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">نام </label>
                                <input type="text" name="first_name" class="form-control" id="first_name"
                                    value="{{ old('first_name', $user->first_name) }}">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">نام خانوادگی</label>
                                <input type="text" name="last_name" class="form-control" id="last_name"
                                    value="{{ old('last_name', $user->last_name) }}">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">تلفن همراه کاربر</label>
                                <input type="text" name="phone" class="form-control" id="phone"
                                    value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="nationalcode" class="form-label">کد ملی کاربر</label>
                                <input type="text" name="nationalcode" class="form-control"
                                    placeholder="کد ملی را وارد کنید"
                                    value="{{ old('nationalcode', $user->nationalcode) }}">
                                @error('nationalcode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <label for="is_active" class="form-label">وضعیت</label>
                            <div class="mt-1">
                                <input type="checkbox" value="1" @checked(old('is_active', $user->is_active)) name="is_active"
                                    id="is_active" data-switch="primary" />
                                <label for="is_active" data-on-label="فعال" data-off-label="غیر فعال"></label>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">ویرایش </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
