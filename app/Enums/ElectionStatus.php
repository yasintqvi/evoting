<?php

namespace App\Enums;

enum ElectionStatus: string
{
    case CREATED = "created";
    case PARTICIPANTS_PENDING = 'participants_pending';
    case PARTICIPANTS_ATTENDEES = "participants_attendees";
    case ONGOING = "ongoing";
    case COMPLETED = "completed";
    case CANCELED = "canceled";

    public function toFa()
    {
        return match ($this) {
            self::CREATED => "ایجاد شده - در انتظار تعیین نامزد ها",
            self::PARTICIPANTS_PENDING => "در انتظار انتخاب مشارکت کنندگان ",
            self::PARTICIPANTS_ATTENDEES => "در انتظار تحقق حد نصاب",
            self::ONGOING => "در حال برگزاری",
            self::COMPLETED => "پایان یافته",
            self::CANCELED => "لغو شده"
        };
    }
}
