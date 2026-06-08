<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Role: string
{
    use EnumValues;

    case Manager = 'admin';

    case Secretary = 'secretary';

    case GroupManager = 'group_manager';
}
