<?php

namespace App\Services;

use App\DTOs\Election\CreateAttendanceDto;
use App\Enums\ElectionStatus;
use App\Models\Election;
use Exception;

class AttendanceService
{
    public function create(CreateAttendanceDto $createAttendanceDto, Election $election)
    {
        foreach ($createAttendanceDto->participantsAttendance as $attendance) {

            $particpant = $election->participants()->find($attendance['participant_id']);

            if ($particpant) {
                $particpant->update([
                    'is_present' => $attendance['status']
                ]);
            }
        }

        foreach ($createAttendanceDto->participantsAttorney as $attorney) {

            $attorneyParticipant = $election->participants()->find($attorney['attorney_id']);

            if ($attorneyParticipant?->attorney_id) {
                throw new Exception("شخصی که به عنوان وکیل انتخاب شده است، نمی‌تواند برای خودش وکیل تعیین کند!");
            }

            $particpant = $election->participants()->find($attorney['participant_id']);

            if ($particpant) {

                $particpant->update([
                    'attorney_id' => $attorneyParticipant?->id
                ]);
            }
        }

        if ($election->precentParticipants() <= 50) {
            throw new Exception('درصد افراد حاضر باید حداقل نصف به علاوه ی یک اعضا باشد.');
        }

        $election->status = ElectionStatus::WAITING_TO_START;

        $election->save();
    }
}
