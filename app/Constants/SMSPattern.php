<?php

namespace App\Constants;

class SMSPattern
{
    public const OTP = 'changePassword';

    public static function getPatterns()
    {
        return [
            self::OTP,
        ];
    }
}
