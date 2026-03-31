<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum TwoFactorType: int
{
    use EnumValues;

    case SMS = 1;
    case GOOGLE_AUTHENTICATOR = 2;

    public static function getTypes(): array
    {
        return [
            self::SMS->value => __('app.sms'),
            self::GOOGLE_AUTHENTICATOR->value => __('app.google_authenticator'),
        ];
    }
}
