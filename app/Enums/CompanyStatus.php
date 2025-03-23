<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum CompanyStatus: int
{
    use EnumValues;
    
    case DISABLE = 0;
    case ENABLE = 1;
}
