<?php

namespace App\DTOs\Election;

use App\DTOs\BaseDataTransferObject;

readonly class ElectionCandidatesDto extends BaseDataTransferObject
{
    public function __construct(
        public array $mainCandidateIds,
        // public array $incpectorCandidatesIds
    ) {}
}
