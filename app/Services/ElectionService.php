<?php

namespace App\Services;

use App\DTOs\Election\CreateElectionDto;
use App\DTOs\Election\UpdateElectionDto;
use App\Enums\ElectionStatus;
use App\Events\ElectionCreated;
use App\Models\Group;
use App\Models\Election;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElectionService
{

    public function getAll(Group $group): Collection
    {
        $elections = $group->elections()->latest()->get();

        return $elections;
    }

    public function create(Group $group, CreateElectionDto $createElectionDto): Group
    {
        DB::beginTransaction();

        try {
            $election = $group->elections()->create($createElectionDto->all());
            event(new ElectionCreated($group, $election));

            DB::commit();

            return $group;
        } catch (Throwable $th) {

            DB::rollBack();

            Log::info("Error while creating election", [
                'message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    public function update(Election $election, UpdateElectionDto $updateElectionDto): void
    {
        $updateData = $updateElectionDto->all();

        $isDirty = false;
        foreach ($updateData as $key => $value) {
            if ($key !== 'title' && $election->$key != $value) {
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
