<?php

namespace App\Enums;

enum AuthType: int
{
    case PASSWORD = 1;
    case OTP = 2;

    public static function getTypes() 
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
