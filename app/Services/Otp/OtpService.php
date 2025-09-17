<?php

namespace App\Services\Otp;

use App\Classes\ValidationCode;
use App\Constants\SMSPattern;
use App\DTOs\Otp\SendOtpDTO;
use App\DTOs\Otp\VerifyOtpDTO;
use App\Enums\TwoFactorType;
use App\Exceptions\Otp\HasActiveCodeException;
use App\Exceptions\User\UserNotExistException;
use App\Facades\SMSFacade;
use App\Models\ShortMessage;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

class OtpService
{
    public function sendOtp(SendOtpDTO $sendOtpDTO)
    {
        $identifier = config('auth.identifier');

        $user = User::where($identifier, $sendOtpDTO->identifier)->first();

        if (!$user) {
            throw new UserNotExistException;
        }

        if ($user->two_factor_type === TwoFactorType::SMS) {
            $this->sendSms($user->phone);
        }

        return [
            'two_factor_type' => $user->two_factor_type,
            'identifier' => $user->$identifier,
            'phone' => $user->phone
        ];
    }

    public function verifyOtp(User $user, VerifyOtpDTO $verifyOtpDTO)
    {
        return match ($user->two_factor_type) {
            TwoFactorType::SMS => $this->checkSMSCode($user->phone, $verifyOtpDTO->otp),
            TwoFactorType::GOOGLE_AUTHENTICATOR => $this->checkGoogleVerify($user->google2fa_secret, $verifyOtpDTO->otp),
            default => true
        };
    }

    protected function sendSms(string $phone)
    {
        $is_allowed_send = ! ShortMessage::hasValidationCode($phone)->first();

        if (! $is_allowed_send) {
            throw new HasActiveCodeException;
        }

        SMSFacade::send($phone, ValidationCode::generate(), SMSPattern::OTP);
    }

    protected function checkSMSCode(string $phone, string $otp_code)
    {
        return ShortMessage::checkValidationCode($phone, $otp_code)->first();
    }

    protected function checkGoogleVerify(string $secret_key, string $authenticator_code)
    {
        $google_2fa = new Google2FA;

        return $google_2fa->verifyKey($secret_key, $authenticator_code);
    }
}
