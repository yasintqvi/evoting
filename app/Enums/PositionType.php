<?php

namespace App\Enums;

enum PositionType
{
    case BOARD_MEMBER;
    case INSPECTOR;
    case CEO;
    case CHAIRMAN;
    case VICE_CHAIRMAN;
    case TREASURER;
    case SECRETARY;
    case SHAREHOLDER_REP;

    public function label(): string
    {
        return match ($this) {
            self::BOARD_MEMBER     => 'عضو هیئت‌مدیره',
            self::INSPECTOR        => 'بازرس',
            self::CEO              => 'مدیرعامل',
            self::CHAIRMAN         => 'رئیس هیئت‌مدیره',
            self::VICE_CHAIRMAN    => 'نایب‌رئیس هیئت‌مدیره',
            self::TREASURER        => 'خزانه‌دار',
            self::SECRETARY        => 'دبیر',
            self::SHAREHOLDER_REP  => 'نماینده سهام‌داران',
        };
    }
}
