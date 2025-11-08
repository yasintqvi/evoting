<?php

namespace App\DTOs\Election;

use App\DTOs\BaseDataTransferObject;
use App\Enums\ElectionType;

readonly class UpdateElectionDto extends BaseDataTransferObject
{
    public function __construct(
        public string $title,
        public bool $quorumRequired,
        public int $ownerId,
        public int $positionId,
        public ElectionType $type,
        public int $mainMemberCount,
        public int $substituteMemberCount,
    ) {
    }
}
