@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">افزودن کاربران به گروه</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item"><a href="#">گروه‌ها</a></li>
                <li class="breadcrumb-item active">افزودن کاربران</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="card col-lg-6">
            <div class="d-flex col-lg-12 align-items-center">
                <div class="card-header border-bottom border-dashed col-lg-6">
                    <h4 class="card-title">اطلاعات مربوط به کاربر</h4>
                    <p class="text-muted mb-0">شما در حال ایجاد کاربر جدید هستید</p>
                </div>
            </div>

            <form id="new-user-form" action="{{ route('group.users.store', $group) }}" method="post"
                enctype="multipart/form-data" style="display: block;">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3 text-center">
                                <label for="avatar" class="form-label d-block">عکس عضو</label>
                                <img id="avatar-preview" src="{{ asset('assets/img/profile.png') }}"
                                    class="avatar-md rounded-circle mb-2" alt="پیش‌نمایش عکس">
                                <input type="file" name="avatar" id="avatar" class="form-control" accept="image/*">
                                @error('avatar')
                                    <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- فیلدهای کاربر --}}
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="productName" class="form-label">نام </label>
                                <input type="text" name="first_name" class="form-control" placeholder="نام"
                                    id="productName" value="{{ old('first_name') }}">
                                @error('first_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="lastName" class="form-label">نام خانوادگی</label>
                                <input type="text" name="last_name" class="form-control" placeholder="نام خانوادگی"
                                    id="lastName" value="{{ old('last_name') }}">
                                @error('last_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">تلفن همراه کاربر</label>
                                <input type="text" name="phone" class="form-control" placeholder="تلفن همراه"
                                    id="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-3 ">
                                <label for="nationalcode" class="form-label">کد ملی کاربر</label>
                                <input type="number" name="nationalcode" class="form-control"
                                    style="direction: rtl; text-align: right;" placeholder="کد ملی را وارد کنید"
                                    value="{{ old('nationalcode') }}">
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
                                                        <span>سهام عادی باقی‌مانده:</span>
                                                        <span class="fw-bold">{{ number_format($remainingNormal) }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <span>سهام ممتاز باقی‌مانده:</span>
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
                                        value="{{ old('normal_stock_count', 0) }}">
                                    @error('normal_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                                    <input type="number" name="prefered_stock_count" class="form-control" min="0"
                                        value="{{ old('prefered_stock_count', 0) }}">
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

                        @if (auth()->user()?->hasRole(\App\Enums\Role::Manager->value))
                            <div class="col-lg-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="as_group_admin"
                                        name="as_group_admin" value="1" @checked(old('as_group_admin'))>
                                    <label class="form-check-label" for="as_group_admin">
                                        افزودن به عنوان مدیر گروه (بدون سهام)
                                    </label>
                                </div>
                                <small class="text-muted">
                                    در این حالت کاربر بدون سهام به گروه اضافه می‌شود و بعد از ثبت، صفحه اعطای دسترسی باز می‌شود.
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">ایجاد</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card col-lg-6">
            <div class="d-flex col-lg-12 align-items-center">
                <div class="card-header border-bottom border-dashed col-lg-6">
                    <h4 class="card-title">افزودن از لیست اعضا</h4>
                    <p class="text-muted mb-0">از لیست اعضای فعلی انتخاب کنید</p>
                </div>
            </div>

            <form id="add-user-form" action="{{ route('group.users.add-batch', $group) }}" method="post"
                style="display: block;">
                @csrf
                <div class="col-12 mt-2 px-3">
                    <div class="mb-3">
                        <label for="users" class="form-label">کاربران </label>
                        <select class="form-control" id="users" data-choices
                            data-choices-removeItem name="users[]" multiple>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(in_array($user->id, old('users', [])))>{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if (auth()->user()?->hasRole(\App\Enums\Role::Manager->value))
                    <div class="col-12 px-3">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="batch_as_group_admin"
                                name="as_group_admin" value="1" @checked(old('as_group_admin'))>
                            <label class="form-check-label" for="batch_as_group_admin">
                                افزودن کاربران انتخاب‌شده به‌عنوان مدیر گروه (بدون سهام)
                            </label>
                        </div>
                    </div>
                @endif
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">افزودن</button>
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

        const asGroupAdminCheckbox = document.getElementById('as_group_admin');
        const normalStockInput = document.querySelector('input[name="normal_stock_count"]');
        const preferedStockInput = document.querySelector('input[name="prefered_stock_count"]');

        function toggleStockInputsByAdminMode() {
            if (!asGroupAdminCheckbox || !normalStockInput || !preferedStockInput) {
                return;
            }

            const managerMode = asGroupAdminCheckbox.checked;
            normalStockInput.readOnly = managerMode;
            preferedStockInput.readOnly = managerMode;

            if (managerMode) {
                normalStockInput.value = 0;
                preferedStockInput.value = 0;
            }
        }

        asGroupAdminCheckbox?.addEventListener('change', toggleStockInputsByAdminMode);
        toggleStockInputsByAdminMode();
    </script>
@endsection
