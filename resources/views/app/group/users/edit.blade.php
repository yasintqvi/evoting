@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ویرایش کاربران</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('group.users.index', $group) }}">کاربران</a></li>
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
                    <p class="text-muted mb-0">شما در حال ویرایش کاربر جدید هستید</p>
                </div>
            </div>

            <!-- فرم ویرایش کاربر جدید -->
            <form id="new-user-form" action="{{ route('group.users.update', [$group, $user]) }}" method="post"
                enctype="multipart/form-data" style="display: block;">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3 text-center">
                                <label for="avatar" class="form-label d-block">عکس عضو</label>
                                <img id="avatar-preview" src="{{ asset($user->profile_image) }}"
                                    class="avatar-md rounded-circle mb-2" alt="پیش‌نمایش عکس">
                                <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                                @error('avatar')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">نام</label>
                                <input type="text" name="first_name" class="form-control" id="productName"
                                    value="{{ old('first_name', $user->first_name) }}">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">نام خانوادگی</label>
                                <input type="text" name="last_name" class="form-control" id="lastName"
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
                                <input type="number" name="nationalcode" class="form-control"
                                    style="direction: rtl; text-align: right;" placeholder="کد ملی را وارد کنید"
                                    value="{{ old('nationalcode', $user->nationalcode) }}">
                                @error('nationalcode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if ($group->type === \App\Enums\GroupType::SPECIAL)
                            <div class="col-12 mb-3">
                                <div class="alert alert-info border-0" role="alert">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="alert-heading mb-1">وضعیت سهام گروه</h5>
                                            <div class="row g-2">
                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span>سهام عادی باقی‌مانده (بدون کسر سهم فعلی کاربر):</span>
                                                        <span class="fw-bold">{{ number_format($remainingNormal) }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span>سهام ممتاز باقی‌مانده (بدون کسر سهم فعلی کاربر):</span>
                                                        <span
                                                            class="fw-bold">{{ number_format($remainingPrefered) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="normal_stock_count" class="form-label">تعداد سهام عادی</label>
                                    <input type="number" name="normal_stock_count" class="form-control" min="0"
                                        value="{{ old('normal_stock_count', $user->pivot->normal_stock_count ?? 0) }}">
                                    @error('normal_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                                    <input type="number" name="prefered_stock_count" class="form-control" min="0"
                                        value="{{ old('prefered_stock_count', $user->pivot->prefered_stock_count ?? 0) }}">
                                    @error('prefered_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-6">
                            <label for="is_active" class="form-label d-block">وضعیت</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" @checked(old('is_active', $user->is_active))>
                                <label class="form-check-label" for="is_active">فعال / غیر فعال</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">ویرایش</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('avatar')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('avatar-preview').src = URL.createObjectURL(file);
            }
        });
    </script>
@endsection
