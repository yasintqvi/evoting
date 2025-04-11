@extends('app.layouts.app')


@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ایجاد کاربران</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">کاربران</a></li>
                <li class="breadcrumb-item active">ایجاد</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="card col-lg-6">
            <!-- بخش بالای فرم -->
            <div class="d-flex col-lg-12 align-items-center">
                <div class="card-header border-bottom border-dashed col-lg-12">
                    <h4 class="card-title">اطلاعات مربوط به کاربر</h4>
                    <p class="text-muted mb-0">شما در حال ایجاد کاربر جدید هستید</p>
                </div>
            </div>

            <!-- فرم ایجاد کاربر -->
            <form id="new-user-form" action="{{ route('company.users.store', $company->slug) }}" method="post"
                style="display: block;">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">نام </label>
                                <input type="text" name="first_name" placeholder="نام را وارد کنید" class="form-control"
                                    id="productName" value="{{ old('first_name') }}">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">نام خانوادگی</label>
                                <input type="text" name="last_name" placeholder="نام خانوادگی را وارد کنید"
                                    class="form-control" id="lastName" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">تلفن همراه کاربر</label>
                                <input type="text" name="phone" placeholder="تلفن همراه را وارد کنید"
                                    class="form-control" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="nationalcode" class="form-label">کد ملی کاربر</label>
                                <input type="text" name="nationalcode" class="form-control"
                                    placeholder="کد ملی را وارد کنید" value="{{ old('nationalcode') }}">
                                @error('nationalcode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if (in_array($company->type, [App\Enums\CompanyType::SPECIAL]))
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="nationalcode" class="form-label">تعداد سهام عادی</label>
                                    <input type="text" name="normal_stock_count" class="form-control"
                                        placeholder="تعداد سهام عادی را وارد کنید" value="{{ old('normal_stock_count') }}">
                                    @error('normal_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    @error('total_stocks')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="nationalcode" class="form-label">تعداد سهام ممتاز</label>
                                    <input type="text" name="prefered_stock_count" class="form-control"
                                        placeholder="تعداد سهام ممتاز را وارد کنید"
                                        value="{{ old('prefered_stock_count') }}">
                                    <input type="hidden" name="total_stocks" value="1">
                                    
                                    @error('prefered_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="col-lg-6">
                            <label for="is_active" class="form-label">وضعیت</label>
                            <div class="mt-1">
                                <input type="checkbox" value="1" @checked(old('is_active')) name="is_active"
                                    id="is_active" data-switch="primary" />
                                <label for="is_active" data-on-label="فعال" data-off-label="غیر فعال"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">ایجاد کاربر جدید</button>
                    </div>
                </div>
            </form>


            <!-- فرم کاربران فعال -->
            <form id="active-users-form" action="{{ route('company.users.store', $company->slug) }}" method="post"
                style="display: none;">
                @csrf
                <div class="card-body">
                    <!-- انتخاب کاربران -->
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label for="users" class="form-label">انتخاب کاربران</label>
                            <select class="form-select my-1 my-md-0 me-sm-3" name="user_ids[]" id="users"
                                data-toggle="select2" multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ collect(old('user_ids'))->contains($user->id) ? 'selected' : '' }}>
                                        {{ $user->fullName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_ids')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-success">افزودن کاربران به شرکت</button>
                    </div>
                </div>
            </form>
        </div>
        @if (in_array($company->type, [App\Enums\CompanyType::SPECIAL]))
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center">
                        <h4 class="header-title">جزئیات سهام شرکت</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            برای دسترسی به حضور غیاب کاربران و شروع انتخابات شرکت باید تعداد کل سهام کاربران با تعداد سهام
                            شرکت
                            برابر باشد .
                        </p>
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <iconify-icon class="fs-20 me-1"></iconify-icon>
                            <div class="lh-1"> تعداد سهام کل شرکت : <strong> {{ $company->total_prefered }} </strong>
                            </div>
                        </div>

                        <div class="alert alert-secondary d-flex align-items-center" role="alert">
                            <iconify-icon class="fs-20 me-1"></iconify-icon>
                            <div class="lh-1">
                                تعداد سهام باقی مانده شرکت : <strong>
                                    {{ $company->total_prefered - $company->assigned_stocks }}
                                </strong>
                            </div>
                        </div>


                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
        @endif


    </div>
@endsection