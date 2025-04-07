<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum ElectionStatus: string
{
    use EnumValues;

    case CREATED = "created";
    case PARTICIPANTS_PENDING = 'participants_pending';
    case PARTICIPANTS_ATTENDEES = "participants_attendees";
    case WAITING_TO_START = "waiting_to_start";
    case ONGOING = "ongoing";
    case COMPLETED = "completed";
    case CANCELED = "canceled";

    public function toFa()
    {
        return match ($this) {
            self::CREATED => "ایجاد شده - در انتظار تعیین نامزد ها",
            self::PARTICIPANTS_PENDING => "در انتظار انتخاب مشارکت کنندگان ",
            self::PARTICIPANTS_ATTENDEES => "در انتظار تحقق حد نصاب",
            self::WAITING_TO_START => "در انتظار شروع انتخابات",
            self::ONGOING => "در حال برگزاری",
            self::COMPLETED => "پایان یافته",
            self::CANCELED => "لغو شده"
        };
    }

    public function isImmutableStatuses()
    {
        return match ($this)  {
            self::ONGOING, self::COMPLETED, self::CANCELED => true,
            default => false
        };
    }
}
