<?php


namespace App\Enums;

use App\Traits\EnumValues;

enum CompanyType: int
{
    use EnumValues;

    case COOPERTAIVE = 1;
    case SPECIAL = 2;
}
