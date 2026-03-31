<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait EnumValues
{
    public static function values(): array
    {
        return Arr::pluck(self::cases(), 'value');
    }
}
