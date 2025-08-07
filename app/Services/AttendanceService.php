<?php

namespace App\Services;

use App\DTOs\Election\CreateAttendanceDto;
use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\Event;
use Exception;

class AttendanceService
{
    public function create(CreateAttendanceDto $createAttendanceDto, Event $event)
    {
        foreach ($createAttendanceDto->participantsAttendance as $attendance) {

            $particpant = $event->participants()->find($attendance['participant_id']);

            if ($particpant) {
                $particpant->update([
                    'is_present' => $attendance['status']
                ]);
            }
        }


        foreach ($createAttendanceDto->participantsAttorney as $attorney) {

            $attorneyParticipant = $event->participants()->find($attorney['attorney_id']);

            if ($attorneyParticipant?->attorney_id) {
                throw new Exception("شخصی که به عنوان وکیل انتخاب شده است، نمی‌تواند برای خودش وکیل تعیین کند!");
            }

            $particpant = $event->participants()->find($attorney['participant_id']);

            if ($particpant) {

                $particpant->update([
                    'attorney_id' => $attorneyParticipant?->id
                ]);
            }
        }

        if (
            $event->precentParticipants() <= 50
        ) {
            throw new Exception('درصد افراد حاضر باید حداقل نصف به علاوه ی یک اعضا باشد.');
        }
        // $event->status = ElectionStatus::WAITING_TO_START;

        $event->save();
    }
}
