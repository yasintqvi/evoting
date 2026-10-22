(() => {
    'use strict';
    const form = document.getElementById('login-form');
    if (!form) return;
    const get = id => document.getElementById(id);
    const phone = get('identifier'), password = get('password'), otp = get('otp');
    const feedback = get('feedback'), submit = get('submit-button');
    const digits = value => value.replace(/[۰-۹٠-٩]/g, c => String('۰۱۲۳۴۵۶۷۸۹'.includes(c) ? '۰۱۲۳۴۵۶۷۸۹'.indexOf(c) : '٠١٢٣٤٥٦٧٨٩'.indexOf(c)));
    const normalizePhone = value => digits(value).replace(/[\s()-]/g, '').replace(/^(?:\+98|0098)/, '0').replace(/^9(?=\d{9}$)/, '09');
    let mode = form.dataset.initialMode, step = 'phone', busy = false;
    let challenge = null;
    try { challenge = JSON.parse(sessionStorage.getItem('loginChallenge')); } catch (_) { /* Storage may be unavailable. */ }
    if (!challenge || typeof challenge.phone !== 'string' || ![1, 2].includes(challenge.type)) challenge = null;
    if (mode === 'otp' && challenge && (!phone.value || phone.value === challenge.phone)) {
        phone.value = challenge.phone;
        step = 'code';
    } else if (mode === 'otp' && form.dataset.codeStep === '1' && phone.value) {
        challenge = {phone: phone.value, type: 1, retryAt: 0};
        step = 'code';
    }
    const store = () => { try { sessionStorage.setItem('loginChallenge', JSON.stringify(challenge)); } catch (_) {} };
    const showMessage = (message, error = false) => {
        feedback.textContent = message;
        feedback.hidden = !message;
        feedback.classList.toggle('is-error', error);
    };
    const setBusy = value => {
        busy = value;
        form.setAttribute('aria-busy', String(value));
        submit.disabled = value;
        document.querySelectorAll('[data-mode], #edit-phone, #resend-code, #use-otp').forEach(button => button.disabled = value);
        submit.querySelector('.spinner').hidden = !value;
        submit.querySelector('.button-arrow').hidden = value;
        get('submit-label').textContent = value ? (mode === 'otp' && step === 'phone' ? 'در حال ارسال کد…' : 'لطفاً صبر کنید…') : (mode === 'otp' ? (step === 'code' ? 'تأیید و ورود به سامانه' : 'دریافت کد یک‌بارمصرف') : 'ورود به سامانه');
        tick();
    };
    function tick() {
        const remaining = Math.max(0, Math.ceil(((challenge?.retryAt || 0) - Date.now()) / 1000));
        get('countdown').textContent = remaining ? `ارسال مجدد تا ${Math.floor(remaining / 60)}:${String(remaining % 60).padStart(2, '0')}` : 'کد را دریافت نکردید؟';
        get('resend-code').disabled = busy || remaining > 0;
    }
    function render() {
        const isOtp = mode === 'otp', isCode = isOtp && step === 'code';
        document.querySelectorAll('[data-mode]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.mode === mode)));
        get('auth-type').value = isOtp ? '2' : '1';
        get('password-field').hidden = isOtp;
        password.disabled = isOtp;
        password.required = !isOtp;
        get('password-alternative').hidden = isOtp;
        get('phone-field').hidden = isCode;
        phone.readOnly = isCode;
        get('phone-summary').hidden = !isCode;
        get('phone-display').textContent = phone.value;
        get('otp-description').hidden = !isOtp || isCode;
        get('code-field').hidden = !isCode;
        otp.disabled = !isCode;
        otp.required = isCode;
        const authenticator = challenge?.type === 2;
        const length = authenticator ? 6 : Number(form.dataset.codeLength);
        otp.maxLength = length;
        otp.pattern = `[0-9]{${length}}`;
        otp.placeholder = '—'.repeat(length);
        get('code-label').textContent = authenticator ? 'کد برنامه Google Authenticator' : 'کد تأیید پیامکی';
        get('code-help').textContent = authenticator ? 'کد ۶ رقمی برنامه تأیید هویت خود را وارد کنید.' : `کد ${length.toLocaleString('fa-IR')} رقمی ارسال‌شده به شماره بالا را وارد کنید.`;
        get('resend-row').hidden = authenticator;
        setBusy(false);
    }
    function switchMode(next) {
        if (busy) return;
        mode = next;
        showMessage('');
        render();
        (mode === 'otp' && step === 'code' ? otp : phone).focus();
    }
    document.querySelectorAll('[data-mode]').forEach(button => button.addEventListener('click', () => switchMode(button.dataset.mode)));
    get('use-otp').addEventListener('click', () => switchMode('otp'));
    get('toggle-password').addEventListener('click', event => {
        const visible = password.type === 'password';
        password.type = visible ? 'text' : 'password';
        event.currentTarget.textContent = visible ? 'پنهان' : 'نمایش';
        event.currentTarget.setAttribute('aria-label', visible ? 'پنهان کردن رمز عبور' : 'نمایش رمز عبور');
        event.currentTarget.setAttribute('aria-pressed', String(visible));
    });
    get('edit-phone').addEventListener('click', () => {
        step = 'phone';
        otp.value = '';
        showMessage('');
        render();
        phone.focus();
    });
    phone.addEventListener('input', () => {
        phone.setCustomValidity('');
        if (normalizePhone(phone.value) !== challenge?.phone) step = 'phone';
    });
    [phone, otp, password].forEach(input => input.addEventListener('input', () => { input.value = digits(input.value); }));
    async function sendCode() {
        if (busy) return;
        phone.value = normalizePhone(phone.value);
        if (!/^09\d{9}$/.test(phone.value)) {
            phone.setCustomValidity('شماره موبایل را به‌صورت ۱۱ رقمی و با ۰۹ وارد کنید.');
            phone.reportValidity();
            return;
        }
        if (challenge?.phone === phone.value && challenge.retryAt > Date.now()) {
            step = 'code';
            render();
            showMessage('کد قبلی هنوز معتبر است. همان کد را وارد کنید.');
            otp.focus();
            return;
        }
        setBusy(true);
        showMessage('');
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 20000);
        try {
            const response = await fetch(form.dataset.sendUrl, {
                method: 'POST', signal: controller.signal,
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
                body: JSON.stringify({identifier: phone.value})
            });
            const data = await response.json().catch(() => ({}));
            const activeCode = response.status === 429 && data.code === 'active_otp';
            if (!response.ok && !activeCode) throw new Error(data.message || (response.status === 419 ? 'نشست شما منقضی شده است؛ صفحه را تازه‌سازی کنید.' : 'ارسال کد انجام نشد. دوباره تلاش کنید.'));
            challenge = {phone: data.identifier || phone.value, type: Number(data.two_factor_type || 1), retryAt: Date.now() + Number(data.retry_after ?? form.dataset.resendSeconds) * 1000};
            store();
            step = 'code';
            otp.value = '';
            render();
            showMessage(activeCode ? 'کد قبلی هنوز معتبر است. همان کد را وارد کنید.' : (challenge.type === 2 ? 'کد برنامه تأیید هویت خود را وارد کنید.' : 'کد تأیید ارسال شد. پیامک‌های خود را بررسی کنید.'));
            otp.focus();
        } catch (error) {
            showMessage(error.name === 'AbortError' ? 'پاسخی دریافت نشد. اتصال اینترنت را بررسی و دوباره تلاش کنید.' : (error instanceof TypeError ? 'ارتباط برقرار نشد. اتصال اینترنت را بررسی کنید.' : error.message), true);
        } finally {
            clearTimeout(timeout);
            setBusy(false);
        }
    }
    get('resend-code').addEventListener('click', sendCode);
    form.addEventListener('submit', event => {
        if (busy) { event.preventDefault(); return; }
        phone.value = normalizePhone(phone.value);
        if (mode === 'otp' && step === 'phone') { event.preventDefault(); sendCode(); return; }
        setBusy(true);
    });
    window.addEventListener('pageshow', () => setBusy(false));
    render();
    setInterval(tick, 1000);
})();
