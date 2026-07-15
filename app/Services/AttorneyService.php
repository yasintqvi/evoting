<?php

namespace App\Services;

use App\DTOs\Attorney\CreateAttorneyDto;
use App\Jobs\SendPasswordJob;
use App\Models\Participant;
use App\Models\User;
use App\Models\Vote;

class AttorneyService
{
    public function create(CreateAttorneyDto $attorneyDto)
    {
        $cleanPhone = preg_replace('/\D/', '', $attorneyDto->user->phone);
        $password = substr($cleanPhone, -4);
        $user = User::firstOrCreate(
            ['phone' => $attorneyDto->user->phone],
            [
                'first_name' => $attorneyDto->user->first_name,
                'last_name' => $attorneyDto->user->last_name,
                'password' => bcrypt($password),
            ]
        );
        $participant = $attorneyDto->participant;
        $deleted = null;
        if ($participant->attorney) {
            $oldAttorney = $participant->attorney;

            // فقط سهام واگذارشدهٔ همین موکل برمی‌گردد؛ سهام شخصی وکیل قبلی (اگر خودش سهام‌دار باشد) دست‌نخورده می‌ماند.
            $returnNormal = (int) $participant->delegated_normal_stock_count;
            $returnPrefered = (int) $participant->delegated_prefered_stock_count;

            if ($returnNormal === 0 && $returnPrefered === 0) {
                $returnNormal = (int) $oldAttorney->normal_stock_count;
                $returnPrefered = (int) $oldAttorney->prefered_stock_count;
            } else {
                $returnNormal = min($returnNormal, (int) $oldAttorney->normal_stock_count);
                $returnPrefered = min($returnPrefered, (int) $oldAttorney->prefered_stock_count);
            }

            $participant->normal_stock_count += $returnNormal;
            $participant->prefered_stock_count += $returnPrefered;

            $hasOtherPrincipals = Participant::query()
                ->where('attorney_id', $oldAttorney->id)
                ->where('id', '!=', $participant->id)
                ->exists();

            // اگر وکیل قبلی موکل دیگری داشته باشد، رأی‌هایش متعلق به همه است و نباید به این موکل منتقل شود.
            if (!$hasOtherPrincipals) {
                Vote::reassignAllFromParticipantTo((int) $oldAttorney->id, (int) $participant->id);
            }

            $oldAttorney->normal_stock_count = max(0, (int) $oldAttorney->normal_stock_count - $returnNormal);
            $oldAttorney->prefered_stock_count = max(0, (int) $oldAttorney->prefered_stock_count - $returnPrefered);
            $oldAttorney->save();

            if (
                !$hasOtherPrincipals &&
                (int) $oldAttorney->normal_stock_count === 0 &&
                (int) $oldAttorney->prefered_stock_count === 0 &&
                !Vote::where('participant_id', $oldAttorney->id)->exists()
            ) {
                $oldAttorney->delete();
                $deleted = $oldAttorney;
            }
        }
        $event = $participant->event;
        $group = $event->group;
        $attorney = $user->participants()
            ->where('event_id', $participant->event_id)
            ->first();

        // سهام کسی که خودش وکالت داده نزد وکیل خودش است؛ اگر وکیل شود سهام موکل روی ردیفی می‌نشیند که در رأی‌گیری دیده نمی‌شود.
        if ($attorney && $attorney->attorney_id) {
            throw new \Exception(__('messages.attorneys.attorney_has_delegated'));
        }

        if (!$attorney) {
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
        if (!$user->groups()->where('groups.id', $group->id)->exists()) {
            $user->groups()->attach($group->id, [
                'normal_stock_count' => 0,
                'prefered_stock_count' => 0,
            ]);
        }

        $participant->attorney_id = $attorney->id;
        $participant->save();
        $participant->load('attorney.user');
        SendPasswordJob::dispatch($password);

        return [$participant, $deleted, $user->wasRecentlyCreated ?? false, $user];
    }
}
