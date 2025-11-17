<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Role: string
{
    use EnumValues;

    case Manager = 'admin';

    case Secretary = 'secretary';

    //group id
    case Group_Manager_Group_Id='group manager';
}
