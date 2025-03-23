<?php

namespace App\Services;

use App\DTOs\Election\CreateElectionDto;
use App\Models\Company;
use App\Models\Participant;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionService
{

    public function getAll(Company $company)
    {
        $elections = $company->elections()->latest()->get();

        return $elections;
    }

    public function create(Company $company, CreateElectionDto $createElectionDto)
    {
        DB::beginTransaction();

        try {

            $election = $company->elections()->create([
                'normal_stock_count' => $company->normal_stock_count,
                'prefered_stock_count' => $company->prefered_stock_count,
                'prefered_stock_weight' =>  $company->prefered_stock_weight,
                ...$createElectionDto->all(),
            ]);

            foreach ($company->users as $user) {
                $election->participants()->create([
                    'user_id' => $user->id,
                    'normal_stock_count' => $user->pivot->normal_stock_count,
                    'prefered_stock_count' => $user->pivot->prefered_stock_count,
                ]);
            }

            DB::commit();
        } catch (Exception $exception) {

            DB::rollBack();

            Log::info("Error while creating election", [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function update() {}

    public function delete() {}
}
