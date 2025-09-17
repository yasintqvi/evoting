@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ویرایش کاربران</h4>
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
                    <p class="text-muted mb-0">شما در حال ویرایش کاربر جدید هستید</p>
                </div>
            </div>

            <!-- فرم ویرایش کاربر جدید -->
            <form id="new-user-form" action="{{ route('group.users.update', [$group->slug, $user->id]) }}" method="post"
                style="display: block;">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
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
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="normal_stock_count" class="form-label">تعداد سهام عادی</label>
                                    <input type="number" name="normal_stock_count" class="form-control" min="0"
                                        value="{{ old('normal_stock_count', $user->pivot->normal_stock_count ?? 0) }}"
                                        @error('normal_stock_count')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                        </div>
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
                            <label for="is_active" class="form-label">وضعیت</label>
                            <div class="mt-1">
                                <input type="checkbox" value="1" name="is_active" id="is_active" data-switch="primary"
                                    {{ old('is_active') ? 'checked' : '' }} />
                                <label for="is_active" data-on-label="فعال" data-off-label="غیر فعال"></label>
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
        {{-- @if (in_array($group->type, [App\Enums\GroupType::SPECIAL]))
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header border-bottom border-dashed d-flex align-items-center">
                        <h4 class="header-title">جزئیات سهام گروه</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center justify-content-center text-center"
                            role="alert">
                            <iconify-icon class="fs-20 me-1"></iconify-icon>
                            <div class="lh-1"> تعداد سهام کل گروه : <strong> {{ $group->total_prefered }} </strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6">
                                <div class=" alert alert-success d-flex align-items-center mb-0" role="alert">
                                    <iconify-icon class="fs-20 me-1"></iconify-icon>
                                    <div class="lh-1">
                                        تعداد کل سهام عادی : <strong>
                                            {{ $group->normal_stock_count }}
                                        </strong>
                                    </div>
                                </div>
                            </div>


                            <div class="col-xl-6">
                                <div class=" alert alert-success d-flex align-items-center mb-0" role="alert">
                                    <iconify-icon class="fs-20 me-1"></iconify-icon>
                                    <div class="lh-1">
                                        تعداد سهام ممتاز : <strong>
                                            {{ $group->prefered_stock_count }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 mt-2">
                                <div class=" alert alert-danger d-flex align-items-center mb-0" role="alert">
                                    <iconify-icon class="fs-20 me-1"></iconify-icon>
                                    <div class="lh-1">
                                        تعداد سهام عادی باقی مانده : <strong>
                                            {{ $group->total_normal_stock }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 mt-2">
                                <div class=" alert alert-danger d-flex align-items-center mb-0" role="alert">
                                    <iconify-icon class="fs-20 me-1"></iconify-icon>
                                    <div class="lh-1">
                                        تعداد سهام ممتاز باقی مانده : <strong>
                                            {{ $group->total_prefered_stock }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div>
        @endif --}}
    </div>
@endsection
