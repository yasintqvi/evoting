@extends('app.layouts.app')

@section('content')

<div id="google-authenticator-helper" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="danger-header-modalLabel">راهنمای فعالسازی Google Authenticator</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ol class="list-group list-group-numbered">
                    <li class="list-group-item">
                        <strong>نصب اپلیکیشن:</strong> برنامه <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Google Authenticator</a> را روی گوشی خود نصب کنید.
                    </li>
                    <li class="list-group-item">
                        <strong>اسکن QR Code:</strong> وارد حساب کاربری خود شوید، به تنظیمات امنیت بروید و QR Code نمایش داده شده را اسکن کنید.
                    </li>
                    <li class="list-group-item">
                        <strong>تایید کد:</strong> کد تولید شده در اپلیکیشن را وارد کنید و فعال‌سازی را تایید کنید.
                    </li>
                    <li class="list-group-item">
                        <strong>ورود با احراز هویت:</strong> هنگام ورود، کد تولیدشده توسط اپلیکیشن را وارد کنید.
                    </li>
                </ol>
                <div class="alert alert-warning mt-3">
                    <strong>نکته:</strong> از کدهای پشتیبان که دریافت می‌کنید، نسخه‌ای امن نگهداری کنید.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">بستن</button>
            </div>
        </div>
    </div>
</div>

<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0"> پروفایل کاربری </h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

            <li class="breadcrumb-item active">پروفایل کاربری</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        @if ($message = session()->get('profile_updated'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <iconify-icon icon="solar:check-read-line-duotone" class="fs-20 me-1"></iconify-icon>
            <div class="lh-1"><strong>موفقیت -</strong>{{$message}}</div>
        </div>
        @endif
        <form action="{{route('profile.update')}}" method="post" enctype="multipart/form-data">
            @csrf
            @method("PUT")
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0">اطلاعات پروفایل</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">نام</label>
                                <input type="text" name="first_name" class="form-control" value="{{ user()->first_name }}" id="first_name" placeholder="نام را وارد کنید" required="">
                                @error('first_name')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">نام خانوادگی</label>
                                <input type="text" class="form-control" value="{{ user()->last_name }}" name="last_name" id="last_name" placeholder="نام خانوادگی را وارد کنید" required="">
                                @error('last_name')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">آدرس ایمیل</label>
                                <input type="email" class="form-control" name="email" value="{{user()->email}}" id="email" placeholder="ایمیل را وارد کنید">
                                @error('email')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">شماره تلفن</label>
                                <input type="number" class="form-control" disabled value="{{user()->phone}}" id="phone" placeholder="شماره را وارد کنید">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="avatar" class="form-label">تصویر پروفایل </label>
                                <input type="file" class="form-control" name="avatar" id="avatar">
                                @error('avatar')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="two_factor_type">نوع احراز هویت دومرحله ای</label>
                                @if (isset(user()->google2fa_secret))
                                <select name="two_factor_type" class="form-control" id="two_factor_type">
                                    @foreach (App\Enums\TwoFactorType::getTypes() as $key => $twoFactorType)
                                    <option @selected($key == user()->two_factor_type->value) value="{{ $key }}">{{ $twoFactorType }}</option>
                                    @endforeach
                                </select>
                                @else 
                                    <input type="text" class="form-control" value="SMS" disabled>
                                @endif
                                @error('two_factor_type')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">بروز رسانی</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        @if ($message = session()->get('password_changed'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <iconify-icon icon="solar:check-read-line-duotone" class="fs-20 me-1"></iconify-icon>
            <div class="lh-1"><strong>موفقیت -</strong>{{ $message }}</div>
        </div>
        @endif
        <form action="{{route('profile.change-password')}}" method="post">
            @csrf
            @method("PUT")
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0">تغییر کلمه عبور</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">کلمه عبور فعلی</label>
                                <input type="password" name="current_password" class="form-control" id="first_name" placeholder="کلمه عبور فعلی" required="">
                                @error('current_password')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="password" class="form-label">کلمه عبور جدید</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="کلمه عبور جدید" required="">
                                @error('password')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تکرار کلمه عبور جدید</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="تکرار کلمه عبور جدید" required="">
                                @error('password_confirmation')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">بروز رسانی</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-4">

        @if ($message = session()->get('google2fa_verified'))
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <iconify-icon icon="solar:check-read-line-duotone" class="fs-20 me-1"></iconify-icon>
            <div class="lh-1"><strong>موفقیت -</strong>{{ $message }}</div>
        </div>
        @endif
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title mb-0">احراز هویت دو مرحله ای با Google Authenticator</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @if (user()->two_factor_type != App\Enums\TwoFactorType::GOOGLE_AUTHENTICATOR)

                    @if(user()->google2fa_secret || session()->get('google_2fa_verify'))
                    <p>اپلیکیشن Google Authenticator را باز کرده و QR Code زیر را اسکن کنید.</p>
                    <div class="w-100">
                        {!! session()->get('google_2fa_verify')['qr_image'] ?? '' !!}
                    </div>
                    <p>یا کلید زیر را دستی وارد کنید:</p>

                    <div class="font-weight-bold mb-2">
                        <code>{{ user()->google2fa_secret }}</code>
                    </div>

                    <form method="POST" action="{{ route('profile.verify-google2fa') }}">
                        @csrf
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="otp" class="form-label">کد تایید</label>
                                <input type="otp" class="form-control" name="otp" id="otp" placeholder="کد تایید" required="">
                                @error('otp')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">فعالسازی</button>
                        </div>
                    </form>
                    @else
                    <form action="{{route('profile.enable-google2fa')}}" method="post">
                        @csrf
                        @method("POST")
                        <a href="!#" type="button" class="my-3" data-bs-toggle="modal" data-bs-target="#google-authenticator-helper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd" d="M22 12c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10M12 7.75c-.621 0-1.125.504-1.125 1.125a.75.75 0 0 1-1.5 0a2.625 2.625 0 1 1 4.508 1.829q-.138.142-.264.267a7 7 0 0 0-.571.617c-.22.282-.298.489-.298.662V13a.75.75 0 0 1-1.5 0v-.75c0-.655.305-1.186.614-1.583c.229-.294.516-.58.75-.814q.106-.105.193-.194A1.125 1.125 0 0 0 12 7.75M12 17a1 1 0 1 0 0-2a1 1 0 0 0 0 2" clip-rule="evenodd" />
                            </svg>
                            راهنمای استفاده
                        </a>
                        <div>
                            <button type="submit" class="btn btn-danger">فعالسازی احراز هویت با Google Authenticator</button>
                        </div>
                    </form>
                    @endif
                    @else
                    احراز هویت دو مرحله ای با google authenticator برای شما فعال است.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection