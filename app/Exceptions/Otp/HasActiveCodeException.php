<?php

namespace App\Exceptions\Otp;

use Exception;
use Illuminate\Http\Response;

class HasActiveCodeException extends Exception
{
    public function __construct()
    {
        $message = __('auth.has_active_code');

        parent::__construct($message, Response::HTTP_UNAUTHORIZED);
    }
}
