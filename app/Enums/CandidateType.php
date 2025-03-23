<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum CandidateType: int
{
    use EnumValues;
    
    case DIRECTOR = 1;
    case INSPECTOR = 2;
}
