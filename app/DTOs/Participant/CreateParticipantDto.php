<?php

namespace App\DTOs\Participant;

use App\DTOs\BaseDataTransferObject;

readonly class CreateParticipantDto extends BaseDataTransferObject
{
    public function __construct(
        public int $electionId,
        public int $userId,
        public int $normalStockCount,
        public int $preferedStockCount,
        public bool $isPresent,
    ) {}
}
