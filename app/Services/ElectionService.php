<?php

namespace App\Services;

use App\DTOs\Election\CreateElectionDto;
use App\DTOs\Election\UpdateElectionDto;
use App\Enums\ElectionStatus;
use App\Events\ElectionCreated;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElectionService
{
    public function getAll(Event $event): Collection
    {
        $elections = $event->elections()->with('position')->latest()->get();

        return $elections;
    }

    public function create(Group $group, Event $event, CreateElectionDto $createElectionDto): Group
    {
        DB::beginTransaction();

        try {
            $election = $event->elections()->create([
                'event_id' => $event->id,
                'normal_stock_count' => $group->normal_stock_count,
                'prefered_stock_count' => $group->prefered_stock_count,
                'prefered_stock_weight' => $group->prefered_stock_weight,
                ...$createElectionDto->all(),
            ]);

            if (!empty($createElectionDto->blockedUserIds)) {
                $election->blockedUsers()->sync($createElectionDto->blockedUserIds);
            }

            DB::commit();

            return $group;
        } catch (Throwable $th) {

            DB::rollBack();

            Log::info('Error while creating election', [
                'message' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ]);

            throw $th;
        }
    }

    public function update(Election $election, UpdateElectionDto $updateElectionDto): void
    {
        $updateData = $updateElectionDto->all();

        // حذف blockedUserIds از داده‌های آپدیت مدل
        $blockedUserIds = $updateData['blockedUserIds'] ?? [];
        unset($updateData['blockedUserIds']);

        $isDirty = false;
        foreach ($updateData as $key => $value) {
            if ($key !== 'title' && $value != $election->$key) {
                $isDirty = true;
                break;
            }
        }

        if ($isDirty) {
            $updateData['status'] = ElectionStatus::CREATED;
        }

        $election->update($updateData);

        // همگام‌سازی کاربران مسدود شده
        if (isset($blockedUserIds)) {
            $election->blockedUsers()->sync($blockedUserIds);
        }
    }

    // public function update(Election $election, UpdateElectionDto $updateElectionDto): void
    // {
    //     $updateData = $updateElectionDto->all();

    //     dd($updateData);
    //     $isDirty = false;
    //     foreach ($updateData as $key => $value) {
    //         if ($key !== 'title' && (string) $value !== (string) $election->$key) {
    //             $isDirty = true;
    //             break;
    //         }
    //     }

    //     if ($isDirty) {
    //         $updateData['status'] = ElectionStatus::CREATED;
    //     }

    //     $election->update($updateData);
    // }

    public function delete(Election $election): ?bool
    {
        return $election->delete();
    }
}
