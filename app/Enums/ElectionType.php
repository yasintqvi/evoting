<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum ElectionType: string
{
    use EnumValues;

    case PUBLIC_JOINT = 'public_joint';
    case PRIVATE_JOINT = 'private_joint';
    case PRIVATE_JOINT_WITH_88 = 'private_joint_with_88';
    case SURVEY = 'survey';

    public function toFa()
    {
        return match ($this) {
            self::PUBLIC_JOINT => 'انتخابات تعاونی',
            self::PRIVATE_JOINT => 'انتخابات سهامی خاص',
            self::PRIVATE_JOINT_WITH_88 => 'انتخابات سهامی خاص با ماده ۸۸',
            self::SURVEY => 'نظرسنجی',
        };
    }
}
