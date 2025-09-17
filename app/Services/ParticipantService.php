<?php

namespace App\Services;

use App\DTOs\Participant\CreateParticipantDto;
use App\Models\Election;
use App\Models\Participant;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ParticipantService
{
    public function getAll(Election $election): Collection
    {
        return $election->participants()->get();
    }

    public function create(CreateParticipantDto $createParticipantDto): Participant
    {
        try {
            $partipant = Participant::create($createParticipantDto->all());

            return $partipant;
        } catch (Exception $exception) {

            Log::info('create particpant failed', ['message' => $exception->getMessage()]);

            throw $exception;
        }
    }
}
