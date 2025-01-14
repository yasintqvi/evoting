<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>ورود | سامانه انتخابات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Theme Config Js -->
    <script src="{{asset('assets/js/config.js')}}"></script>

    <!-- Vendor css -->
    <link href="{{asset('assets/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
        <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">

                    <h3 class="fw-semibold mb-2"> ورود به سامانه انتخابات</h3>

                    <p class="text-muted mb-4">خوش آمدید</p>

                    <form action="{{ route('login') }}" method="post" class="text-start mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="identifier">شناسه کاربری</label>
                            <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" class="form-control" placeholder="شناسه کاربری">
                            <span id="identitifier-error" class="text-danger"></span>
                            @error('identifier')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="hidden" name="auth_type" value="{{ App\Enums\AuthType::PASSWORD->value }}">
                        <div class="mb-3">
                            <label class="form-label" for="password">کلمه عبور</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="رمز ورود خود را وارد کنید">
                            @error('password')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                <label class="form-check-label" name="remember" name="remember" @checked(old('remember')) for="checkbox-signin">من را به یاد بیاورید</label>
                            </div>

                            <a href="auth-recoverpw.html" class="text-muted border-bottom border-dashed">فراموشی رمز عبور</a>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary col-md-6" type="submit">ورود</button>
                            <a href="" onclick="sendOtp(event)" class="btn btn-success col-md-6" type="submit">ورود با کد یکبار مصرف</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="{{asset('assets/js/vendor.min.js')}}"></script>

    <!-- App js -->
    <script src="{{asset('assets/js/app.js')}}"></script>

    <script>
        function sendOtp(event) {
            event.preventDefault();

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const identitifier = document.querySelector('input[name=identifier]').value;

            fetch("/otp/send", {
                    method: "POST",
                    body: JSON.stringify({
                        "identifier": identitifier
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                }).then(response => response.json())
                .then(data => {

                    if (!data.success && data.status === 422) {
                        document.getElementById('identitifier-error').innerHTML = data.message;
                    } else {
                        if (!data.status) {
                            localStorage.setItem('identifier', identitifier);
                            localStorage.setItem('two_factor_type', data['two_factor_type']);
                            window.location.href = "login/otp";
                        }
                    }

                });
        }
    </script>

</body>

</html>