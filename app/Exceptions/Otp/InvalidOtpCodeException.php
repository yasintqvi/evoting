<?php

namespace App\Exceptions\Otp;

use Exception;
use Illuminate\Http\Response;

class InvalidOtpCodeException extends Exception
{
    public function __construct()
    {
        $message = __('auth.invalid_otp_code');

        parent::__construct($message, Response::HTTP_UNAUTHORIZED);
    }
}
