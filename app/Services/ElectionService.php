<?php

namespace App\Services;

use App\DTOs\Election\CreateElectionDto;
use App\DTOs\Election\UpdateElectionDto;
use App\Events\ElectionCreated;
use App\Models\Company;
use App\Models\Election;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElectionService
{

    public function getAll(Company $company)
    {
        $elections = $company->elections()->latest()->get();

        return $elections;
    }

    public function create(Company $company, CreateElectionDto $createElectionDto): Company
    {
        DB::beginTransaction();

        try {
            $election = $company->elections()->create([
                'normal_stock_count' => $company->normal_stock_count,
                'prefered_stock_count' => $company->prefered_stock_count,
                'prefered_stock_weight' =>  $company->prefered_stock_weight,
                'title' => $createElectionDto->title,
                'owner_id' => $createElectionDto->owner_id,
                'type' => $createElectionDto->type,
                'main_member_count' => $createElectionDto->mainMemberCount,
                'substitute_member_count' => $createElectionDto->substituteMemberCount,
                'incpector_main_member_count' => $createElectionDto->incpectorMainMemberCount,
                'incpector_substitute_member_count' => $createElectionDto->incpectorSubstituteMemberCount,
                'quorum_required' => $createElectionDto->quorumRequired,
            ]);
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

    public function update(Election $election, UpdateElectionDto $updateElectionDto)
    {
        $updateData = [
            'title' => $updateElectionDto->title,
            'quorum_required' => $updateElectionDto->quorumRequired,
            'main_member_count' => $updateElectionDto->mainMemberCount,
            'substitute_member_count' => $updateElectionDto->substituteMemberCount,
            'incpector_main_member_count' => $updateElectionDto->incpectorMainMemberCount,
            'incpector_substitute_member_count' => $updateElectionDto->incpectorSubstituteMemberCount,
        ];

        $isDirty = false;
        foreach ($updateData as $key => $value) {
            if ($key !== 'title' && $election->$key != $value) {
                $isDirty = true;
                break;
            }
        }

        if ($isDirty) {
            $updateData['status'] = 'created';
        }

        $election->update($updateData);
    }

    public function delete(Election $election): ?bool
    {
        return $election->delete();
    }
}
