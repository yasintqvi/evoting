<?php

namespace App\Services;

use App\DTOs\Attorney\CreateAttorneyDto;
use App\DTOs\Attorney\DeleteAttorneyDto;
use App\Jobs\SendPasswordJob;
use App\Models\User;
use Illuminate\Support\Str;

class AttorneyService
{
    public function create(CreateAttorneyDto $attorneyDto)
    {
        $password = substr($attorneyDto->user->phone,-4);
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
            $participant->attorney()->delete();
        }
        $event = $participant->event;
        $group = $event->group;
        $attorney = $user->participants()->create([
            'event_id' => $participant->event_id,
            'normal_stock_count' => $participant->normal_stock_count,
            'prefered_stock_count' => $participant->prefered_stock_count,
            'is_present' => 0,
        ]);

        $participant->is_present=1;

        $user->groups()->syncWithoutDetaching([$group->id]);

        $participant->attorney_id = $attorney->id;
        $participant->save();
        $participant->load('attorney.user');
        SendPasswordJob::dispatch($password);

        return [$participant, $deleted];
    }

    public function delete(DeleteAttorneyDto $attorneyDto)
    {
        $participant = $attorneyDto->participant;
        $event = $participant->event;
        $group = $event->group;
        $user = $participant->user;
        if ($event->participants()->where('user_id', $user->id)->count() < 2) {
            $user->groups()->detach($group->id);
        }

        $status = $participant->delete();

        return $status;

    }
}
