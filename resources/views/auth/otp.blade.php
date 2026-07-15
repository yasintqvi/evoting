<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title>ورود | سامانه انتخابات</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vendor css -->
    <link href="{{asset('assets/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
</head>

<body>

    <div class="auth-bg d-flex min-vh-100 justify-content-center align-items-center">
        <div class="row g-0 justify-content-center w-100 m-xxl-5 px-xxl-4 m-3">
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div id="resend-success" class="alert alert-success d-flex align-items-center d-none" role="alert">
                    <iconify-icon icon="solar:check-read-line-duotone" class="fs-20 me-1"></iconify-icon>
                    <div class="lh-1"><strong>موفق &nbsp; </strong>کد یکبار مصرف مجددا ارسال شد.</div>
                </div>
                <div class="card overflow-hidden text-center h-100 p-xxl-4 p-3 mb-0">

                    <h3 class="fw-semibold mb-2"> ورود به سامانه انتخابات</h3>
                    <p class="text-muted mb-4">خوش آمدید</p>

                    <form action="{{ route('login') }}" method="post" class="text-start mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="identifier">شناسه کاربری</label>
                            <input type="text" id="identifier" name="identifier" value="{{ request()->get('identifier') }}" class="form-control" placeholder="شناسه کاربری">
                            @error('identifier')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <input type="hidden" name="auth_type" value="{{ App\Enums\AuthType::OTP->value }}">
                        <div class="mb-3">
                            <label class="form-label" id="otp-label" for="otp"></label>
                            <input type="text" id="otp" name="otp" class="form-control" placeholder="کد یکبار مصرف">
                            @error('otp')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                <label class="form-check-label" name="remember" @checked(old('remember')) for="checkbox-signin">من را به یاد بیاورید</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary col-md-6" type="submit">ورود</button>
                            <a href="{{route('login.form')}}" class="btn btn-success col-md-6">ورود با کلمه عبور</a>
                        </div>
                    </form>

                    <div class="mt-4">
                        <p id="countdown" class="text-danger fw-bold d-none"></p>
                        <a id="resend-link" href="#" onclick="sendOtp(event)" class="btn btn-link d-none">ارسال مجدد کد</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="{{asset('assets/js/vendor.min.js')}}"></script>

    <!-- Persian -> English digit conversion for all inputs -->
    <script src="{{asset('assets/js/persian-numbers.js')}}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const identifierInput = document.querySelector('input[name=identifier]');
            identifierInput.value = localStorage.getItem('identifier') || '';

            identifierInput.addEventListener('input', () => {
                localStorage.setItem('identifier', identifierInput.value);
            });

            const twoFactorType = localStorage.getItem('two_factor_type');
            const otpLabel = document.getElementById('otp-label');
            const countdownElement = document.getElementById('countdown');
            const resendLink = document.getElementById('resend-link');

            let countdownInterval;

            if (twoFactorType == 1) {

                otpLabel.textContent = "کد یکبار مصرف";

                let expireMinutes = "{{config('auth.constants.new_sms_expire_minutes')}}";

                function startCountdown() {
                    clearInterval(countdownInterval);
                    countdownElement.classList.remove('d-none');
                    resendLink.classList.add('d-none');

                    let remainingTime = expireMinutes * 60;

                    countdownInterval = setInterval(() => {
                        if (remainingTime > 0) {
                            const minutes = Math.floor(remainingTime / 60);
                            const seconds = remainingTime % 60;
                            countdownElement.textContent = `ارسال مجدد کد تا ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
                            remainingTime--;
                        } else {
                            clearInterval(countdownInterval);
                            countdownElement.classList.add('d-none');
                            resendLink.classList.remove('d-none');
                        }
                    }, 1000);
                }

                startCountdown();

                window.sendOtp = function(event) {
                    event.preventDefault();

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const identifier = document.querySelector('input[name=identifier]').value;

                    fetch("/otp/send", {
                            method: "POST",
                            body: JSON.stringify({
                                "identifier": identifier
                            }),
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                        }).then(response => response.json())
                        .then(data => {
                            if (!data.success && data.status === 422) {
                                const identifierErrorElement = document.getElementById('identitifier-error');
                                if (identifierErrorElement) {
                                    identifierErrorElement.innerHTML = data.message;
                                }
                            } else {
                                localStorage.setItem('identifier', identifier);
                                localStorage.setItem('two_factor_type', data['two_factor_type']);
                                document.getElementById('resend-success').classList.remove('d-none');
                                startCountdown();
                            }
                        });
                }
            } else {
                otpLabel.textContent = twoFactorType == 2 ? "کد Google Authenticator" : "کد تأیید";
            }
        });
    </script>

</body>

</html>