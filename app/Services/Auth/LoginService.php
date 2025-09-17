<?php

namespace App\Services\Auth;

use App\DTOs\Auth\LoginDTO;
use App\DTOs\Otp\VerifyOtpDTO;
use App\Enums\UserStatus;
use App\Exceptions\Auth\UserHasBeenBlockedException;
use App\Exceptions\Otp\InvalidOtpCodeException;
use App\Models\User;
use App\Services\Otp\OtpService as OtpOtpService;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function __construct(protected OtpOtpService $otpService) {}

    public function loginWithPassword(LoginDTO $loginDTO)
    {
        $identifier_field = config('auth.identifier');

        $user = User::where($identifier_field, $loginDTO->identifier)->first();

        if (!$user || !$user->password) {
            return false;
        }

        $this->checkUserStatus($user);

        return Auth::attempt([
            $identifier_field => $loginDTO->identifier,
            'password' => $loginDTO->password
        ], $loginDTO->remember);
    }

    public function loginWithOtp(LoginDTO $loginDTO)
    {

        $identifier_field = config('auth.identifier');

        $user = User::where($identifier_field, $loginDTO->identifier)->first();

        if (!$user) {
            return false;
        }

        $this->checkUserStatus($user);

        $response = $this->otpService->verifyOtp($user, new VerifyOtpDTO(
            $loginDTO->otp
        ));

        if (!$response) {
            throw new InvalidOtpCodeException;
        }

        Auth::login($user);
    }

    protected function checkUserStatus(User $user)
    {
        if ($user->status === UserStatus::BLOCK) {
            throw new UserHasBeenBlockedException;
        }
    }
}
