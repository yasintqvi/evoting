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
        $elections = $event->elections()->latest()->get();

        return $elections;
    }

    public function create(Group $group, Event $event, CreateElectionDto $createElectionDto): Group
    {
        DB::beginTransaction();

        try {
            $election = $event->elections()->create([
                'group_id' => $group->id,
                ...$createElectionDto->all(),
            ]);
            event(new ElectionCreated($group, $election));

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
    }

    public function delete(Election $election): ?bool
    {
        return $election->delete();
    }
}
