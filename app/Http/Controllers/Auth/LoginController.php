<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuthType;
use App\Exceptions\Auth\UserHasBeenBlockedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function __construct(protected LoginService $loginService) {}

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $loginDto = $request->toDTO();

        try {
            $loginAttempt = match ($loginDto->auth_type) {
                AuthType::PASSWORD->value => $this->loginService->loginWithPassword($loginDto),
                AuthType::OTP->value => $this->loginService->loginWithOtp($loginDto),
                default => throw new Exception
            };

            if (!$loginAttempt) {
                return to_route('login.form')->withErrors([
                    'password' => __('auth.failed')
                ]);
            }

            return to_route('app.index');
        } catch (UserHasBeenBlockedException $e) {
            return to_route('login.form')->withErrors([
                'password' => $e->getMessage()
            ]);
        } catch (Exception $e) {

            Log::error("The login operation encountered a system error. message : {$e->getMessage()}", ['trace' => $e->getTrace()]);

            return to_route('login.form')->withErrors([
                'password' => __('auth.failed')
            ]);
        }
    }

    public function otpForm() {}

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return to_route('login');
    }
}
