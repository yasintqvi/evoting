<?php

namespace App\Enums;

enum TwoFactorType: int
{
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
