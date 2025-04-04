<?php

namespace App\Services;

use App\DTOs\Election\CreateElectionDto;
use App\DTOs\Election\UpdateElectionDto;
use App\Enums\ElectionStatus;
use App\Events\ElectionCreated;
use App\Models\Company;
use App\Models\Election;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElectionService
{

    public function getAll(Company $company): Collection
    {
        $elections = $company->elections()->latest()->get();

        return $elections;
    }

    public function create(Company $company, CreateElectionDto $createElectionDto): Company
    {
        DB::beginTransaction();

        try {
            $election = $company->elections()->create($createElectionDto->all());
            event(new ElectionCreated($company, $election));

            DB::commit();

            return $company;
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
