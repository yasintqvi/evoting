<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum AuthType: int
{
    use EnumValues;

    case PASSWORD = 1;
    case OTP = 2;

    public static function getTypes()
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
