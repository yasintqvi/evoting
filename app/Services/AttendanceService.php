<?php

namespace App\Services;

use App\DTOs\Election\CreateAttendanceDto;
use App\Models\Event;
use App\Models\Participant;
use App\Models\User;
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

            if ($attorney['attorney_id']) {
                $attorneyParticipant = User::where('phone', $attorney['attorney_id'])->first();

                if (!$attorneyParticipant) {
                    $attorneyParticipant = User::create([
                        'phone' => $attorney['attorney_id'],
                        "passowrd" => 1234578,
                        "first_name" => 'test'
                    ]);
                }

                if ($attorneyParticipant?->attorney_id) {
                    throw new Exception("شخصی که به عنوان وکیل انتخاب شده است، نمی‌تواند برای خودش وکیل تعیین کند!");
                }

                $particpant = $event->participants()->find($attorney['participant_id']);

                if ($particpant) {

                    $attorney = Participant::create([
                        'user_id' => $attorneyParticipant->id,
                        'event_id' => $event->id,
                        'normal_stock_count' => $particpant->normal_stock_count,
                        'prefered_stock_count' => $particpant->prefered_stock_count,
                        'is_present' => true
                    ]);


                    $particpant->update([
                        'attorney_id' => $attorney->id,
                        'normal_stock_count' => 0,
                        'prefered_stock_count' => 0,
                    ]);
                }
            }
        }

        $event->save();
    }
}
