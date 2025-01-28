<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuthType;
use App\Exceptions\Auth\UserHasBeenBlockedException;
use App\Exceptions\Otp\InvalidOtpCodeException;
use App\Exceptions\User\UserNotExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Services\Auth\LoginService;
use App\Services\Otp\OtpService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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

            if (!$loginAttempt) {
                return to_route('login.form')->withErrors([
                    'password' => __('auth.failed')
                ]);
            }

            return to_route('app.index')->with('success', __('auth.success'));
        } catch (UserHasBeenBlockedException $e) {
            return to_route('login.form')->withErrors([
                'password' => $e->getMessage()
            ]);
        } catch (InvalidOtpCodeException $e) {
            return to_route('otp.form')->withErrors([
                'otp' => $e->getMessage()
            ]);
        } catch (Exception $e) {

            Log::critical("The login operation encountered a system error. message : {$e->getMessage()}", ['trace' => $e->getTrace()]);

            return to_route('login.form')->withErrors([
                'password' => __('auth.failed')
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
        } catch (UserNotExistException $e) {

            return response()->noContent(Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::critical("The send otp operation encountered a system error. message: {$e->getMessage()}", ['trace' => $e->getTrace()]);

            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
