<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuthType;
use App\Exceptions\Auth\UserHasBeenBlockedException;
use App\Exceptions\Otp\HasActiveCodeException;
use App\Exceptions\Otp\InvalidOtpCodeException;
use App\Exceptions\User\UserNotExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Models\ShortMessage;
use App\Services\Auth\LoginService;
use App\Services\Otp\OtpService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        protected LoginService $loginService,
        protected OtpService $otpService
    ) {}

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

            if (! $loginAttempt) {
                return to_route('login.form')->withInput($request->only('identifier', 'auth_type', 'remember'))->withErrors([
                    'password' => __('auth.failed'),
                ]);
            }

            $request->session()->regenerate();

            return to_route('app.index')->with('success', __('auth.success'));
        } catch (UserHasBeenBlockedException $e) {
            return to_route('login.form')->withInput($request->only('identifier', 'auth_type', 'remember'))->withErrors([
                'password' => $e->getMessage(),
            ]);
        } catch (InvalidOtpCodeException $e) {
            return to_route('otp.form')->withInput($request->only('identifier', 'auth_type', 'remember'))->withErrors([
                'otp' => $e->getMessage(),
            ]);
        } catch (Exception $e) {

            Log::critical("The login operation encountered a system error. message : {$e->getMessage()}", ['trace' => $e->getTrace()]);

            return to_route('login.form')->withInput($request->only('identifier', 'auth_type', 'remember'))->withErrors([
                'password' => __('auth.failed'),
            ]);
        }
    }

    public function otpForm()
    {
        return view('auth.otp');
    }

    public function sendOtp(SendOtpRequest $request)
    {
        try {
            $response = $this->otpService->sendOtp($request->toDTO());

            return response()->json($response);
        } catch (HasActiveCodeException $e) {
            $message = ShortMessage::hasValidationCode($request->validated('identifier'))->first();
            $retryAfter = $message
                ? max(1, now()->diffInSeconds($message->created_at->addMinutes(config('auth.constants.new_sms_expire_minutes')), false))
                : 1;

            return response()->json([
                'code' => 'active_otp',
                'message' => $e->getMessage(),
                'identifier' => $request->validated('identifier'),
                'two_factor_type' => 1,
                'retry_after' => (int) ceil($retryAfter),
            ], 429)->header('Retry-After', (string) (int) ceil($retryAfter));
        } catch (UserNotExistException $e) {

            return response()->json([
                'message' => 'کاربری با این شماره ثبت نشده است.',
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::critical("The send otp operation encountered a system error. message: {$e->getMessage()}", ['trace' => $e->getTrace()]);

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
