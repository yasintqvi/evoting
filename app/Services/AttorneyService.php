<?php

namespace App\Services;

use App\DTOs\Attorney\CreateAttorneyDto;
use App\Jobs\SendPasswordJob;
use App\Models\User;
use App\Models\Vote;

class AttorneyService
{
    public function create(CreateAttorneyDto $attorneyDto)
    {
        $password = substr($attorneyDto->user->phone, -4);
        $user = User::firstOrCreate(
            ['phone' => $attorneyDto->user->phone],
            [
                'first_name' => $attorneyDto->user->first_name,
                'last_name' => $attorneyDto->user->last_name,
                'password' => bcrypt($password),
            ]
        );
        $participant = $attorneyDto->participant;
        $deleted = $participant->attorney;
        if ($participant->attorney) {
            $oldAttorney = $participant->attorney;
            $participant->normal_stock_count += (int) $oldAttorney->normal_stock_count;
            $participant->prefered_stock_count += (int) $oldAttorney->prefered_stock_count;
            Vote::reassignAllFromParticipantTo((int) $oldAttorney->id, (int) $participant->id);
            $oldAttorney->normal_stock_count = 0;
            $oldAttorney->prefered_stock_count = 0;
            $oldAttorney->delegated_normal_stock_count = 0;
            $oldAttorney->delegated_prefered_stock_count = 0;
            $oldAttorney->save();

            if (! Vote::where('participant_id', $oldAttorney->id)->exists()) {
                $oldAttorney->delete();
            }
        }
        $event = $participant->event;
        $group = $event->group;
        $attorney = $user->participants()
            ->where('event_id', $participant->event_id)
            ->first();

        if (! $attorney) {
            $attorney = $user->participants()->create([
                'event_id' => $participant->event_id,
                'group_id' => $group->id,
                'normal_stock_count' => 0,
                'prefered_stock_count' => 0,
                'is_present' => 1,
            ]);
        }

        $participant->delegated_normal_stock_count = (int) $participant->normal_stock_count;
        $participant->delegated_prefered_stock_count = (int) $participant->prefered_stock_count;

        // هم‌راستا با حضور و غیاب گروهی: سهام موکل به وکیل منتقل می‌شود تا ثبت رأی و UI یکسان باشد.
        $attorney->normal_stock_count += (int) $participant->normal_stock_count;
        $attorney->prefered_stock_count += (int) $participant->prefered_stock_count;
        $attorney->is_present = true;
        $attorney->save();

        $participant->normal_stock_count = 0;
        $participant->prefered_stock_count = 0;

        $participant->is_present = 1;

        // syncWithoutDetaching فقط id گروه بدهد، روی pivot ردیف جدید پیش‌فرض دیتابیس (۱ سهام عادی) می‌خورد.
        if (! $user->groups()->where('groups.id', $group->id)->exists()) {
            $user->groups()->attach($group->id, [
                'normal_stock_count' => 0,
                'prefered_stock_count' => 0,
            ]);
        }

        $participant->attorney_id = $attorney->id;
        $participant->save();
        $participant->load('attorney.user');
        SendPasswordJob::dispatch($password);

        return [$participant, $deleted];
    }
}
