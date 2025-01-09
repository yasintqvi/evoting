<?php

namespace App\Enums;

enum TwoFactorType: int
{
    case SMS = 1;
    case GOOGLE_AUTHENTICATOR = 2;
}
