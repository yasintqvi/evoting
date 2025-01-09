<?php

namespace App\Enums;

enum AuthType: int
{
    case PASSWORD = 1;
    case OTP = 2;
}
