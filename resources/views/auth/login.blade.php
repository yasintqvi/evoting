<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#465dff">
    <title>ورود به حساب | سامانه انتخابات</title>
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    <script src="{{ asset('assets/js/auth.js') }}" defer></script>
</head>
<body>
    <main class="auth-shell">
        <section class="form-panel" aria-labelledby="form-title">
            <div class="form-wrap">
                <div class="form-brand"><span class="brand-mark" aria-hidden="true">✓</span>سامانه انتخابات</div>
                <span class="section-kicker">خوش آمدید</span>
                <h2 id="form-title">ورود به حساب کاربری</h2>
                <p class="form-intro">روش ورود خود را انتخاب کنید.</p>

                <div class="method-switch" role="group" aria-label="روش ورود">
                    <button type="button" id="password-method" aria-pressed="true" data-mode="password">ورود با رمز عبور</button>
                    <button type="button" id="otp-method" aria-pressed="false" data-mode="otp">کد یک‌بارمصرف</button>
                </div>

                <div id="feedback" class="feedback {{ $errors->any() ? 'is-error' : '' }}" role="status" aria-live="polite" tabindex="-1" @if(!$errors->any()) hidden @endif>
                    {{ $errors->first() }}
                </div>

                <form id="login-form" action="{{ route('login') }}" method="post"
                    data-send-url="{{ route('otp.send') }}"
                    data-initial-mode="{{ (int) old('auth_type', isset($otpPage) ? App\Enums\AuthType::OTP->value : App\Enums\AuthType::PASSWORD->value) === App\Enums\AuthType::OTP->value ? 'otp' : 'password' }}"
                    data-code-step="{{ old('auth_type') == App\Enums\AuthType::OTP->value ? '1' : '0' }}"
                    data-code-length="{{ config('auth.constants.code_length', 5) }}"
                    data-resend-seconds="{{ config('auth.constants.new_sms_expire_minutes', 3) * 60 }}">
                    @csrf
                    <input type="hidden" id="auth-type" name="auth_type" value="{{ App\Enums\AuthType::PASSWORD->value }}">
                    <div class="field" id="phone-field">
                        <label for="identifier">شماره موبایل <span>نام کاربری شما</span></label>
                        <input type="tel" id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="09xxxxxxxxx" autocomplete="username" inputmode="tel" dir="ltr" required aria-describedby="phone-help">
                        <p class="field-help" id="phone-help">شماره موبایلی که با آن در سامانه ثبت شده‌اید.</p>
                    </div>
                    <div class="phone-summary" id="phone-summary" hidden>
                        <div><small>شماره موبایل</small><b id="phone-display" dir="ltr"></b></div>
                        <button type="button" id="edit-phone" class="text-button">ویرایش شماره</button>
                    </div>
                    <div class="field" id="password-field">
                        <label for="password">رمز عبور</label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" autocomplete="current-password" placeholder="رمز عبور خود را وارد کنید" required aria-describedby="password-help">
                            <button type="button" id="toggle-password" aria-label="نمایش رمز عبور" aria-pressed="false">نمایش</button>
                        </div>
                        <p class="field-help" id="password-help">رمز پیش‌فرض: چهار رقم آخر کد ملی شما.</p>
                    </div>
                    <div class="otp-description" id="otp-description" hidden>کد تأیید برای شماره موبایل ثبت‌شده شما ارسال می‌شود؛ نیازی به وارد کردن رمز عبور ندارید.</div>
                    <div class="field" id="code-field" hidden>
                        <label for="otp" id="code-label">کد تأیید</label>
                        <input type="text" id="otp" name="otp" class="code-input" inputmode="numeric" autocomplete="one-time-code" dir="ltr" aria-describedby="code-help" disabled>
                        <p class="field-help" id="code-help"></p>
                        <div class="resend-row" id="resend-row">
                            <span id="countdown"></span>
                            <button type="button" class="text-button" id="resend-code">ارسال مجدد کد</button>
                        </div>
                    </div>
                    <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>مرا به خاطر بسپار</span></label>
                    <button class="primary-button" type="submit" id="submit-button"><span id="submit-label">ورود به سامانه</span><span class="button-arrow" aria-hidden="true">←</span><span class="spinner" aria-hidden="true" hidden></span></button>
                    <p class="alternative" id="password-alternative">رمز عبور را ندارید؟ <button type="button" class="text-button" id="use-otp">با کد یک‌بارمصرف وارد شوید</button></p>
                    <noscript><p class="field-help">برای دریافت کد یک‌بارمصرف، جاوااسکریپت مرورگر را فعال کنید. ورود با رمز عبور در دسترس است.</p></noscript>
                </form>            </div>
        </section>
    </main>
</body>
</html>
