<?php

namespace App\Services;

use App\DTOs\Election\ElectionCandidatesDto;
use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\Group;
use Event;
use Exception;
use Log;

class CandidateService
{
    public function create(Election $election, ElectionCandidatesDto $addCandidatesDto)
    {
        $this->ensureCandidatesNotDuplicate($addCandidatesDto);

        $this->ensureCanAddCandidates($election);

        $election->candidates()->delete();

        foreach ($addCandidatesDto->mainCandidateIds as $mainCandidateId) {
            $election->candidates()->create([
                'user_id' => $mainCandidateId,
                'candidate_type' => CandidateType::DIRECTOR,
            ]);
        }

        foreach ($addCandidatesDto->incpectorCandidatesIds as $incpectorCandidateId) {
            $election->candidates()->create([
                'user_id' => $incpectorCandidateId,
                'candidate_type' => CandidateType::INSPECTOR,
            ]);
        }

        $election->status = ElectionStatus::PARTICIPANTS_ATTENDEES;

        $election->save();
    }

    public function update(Election $election, ElectionCandidatesDto $dto): void
    {
        $this->ensureCandidatesNotDuplicate($dto);

        $this->ensureCanAddCandidates($election);

        $election->candidates()
            ->whereNotIn('user_id', array_merge($dto->mainCandidateIds, $dto->incpectorCandidatesIds))
            ->delete();

        foreach ($dto->mainCandidateIds as $mainCandidateId) {
            $election->candidates()->updateOrCreate(
                ['user_id' => $mainCandidateId],
                ['candidate_type' => CandidateType::DIRECTOR]
            );
        }

        foreach ($dto->incpectorCandidatesIds as $incpectorCandidateId) {
            $election->candidates()->updateOrCreate(
                ['user_id' => $incpectorCandidateId],
                ['candidate_type' => CandidateType::INSPECTOR]
            );
        }

        $election->status = $election->quorum_required ? ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

        $election->save();
    }

 


    private function ensureCanAddCandidates(Election $election): void
    {
        if ($election->status->isImmutableStatuses()) {
            throw new Exception('امکان ویرایش در این وضعیت وجود ندارد.');
        }
    }

    private function ensureCandidatesNotDuplicate(ElectionCandidatesDto $addCandidatesDto)
    {
        $duplicateCandidates = array_intersect($addCandidatesDto->mainCandidateIds, $addCandidatesDto->incpectorCandidatesIds);

        if (!empty($duplicateCandidates)) {
            throw new Exception('یک نامزد نمی‌تواند همزمان در هیئت مدیره و بازرس باشد.');
        }
    }
}
