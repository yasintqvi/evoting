<?php

namespace App\Enums;

enum ElectionType: string
{
    case PUBLIC_JOINT = "public_joint";
    case COOPERTAIVE = "cooperative";
    case SPECIAL = "special";

    case PRIVATE_JOINT = "private_joint";

    case PRIVATE_JOINT_WITH_88 = "private_joint_with_88";

    public function toFa()
    {
        return match ($this) {
            self::PUBLIC_JOINT => 'انتخابات تعاونی',
            self::PRIVATE_JOINT => 'انتخابات سهامی خاص',
            self::PRIVATE_JOINT_WITH_88 => 'انتخابات سهامی خاص با ماده ۸۸',
            self::COOPERTAIVE => 'شرکت تعاونی',
            self::SPECIAL => 'شرکت سهامی خاص',
        };
    }
}
