<?php


namespace App\Enums;

enum CompanyType: int
{
    case COOPERTAIVE = 0;
    case SPECIAL = 1;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}