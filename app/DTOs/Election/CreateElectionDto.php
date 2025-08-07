<?php

namespace App\DTOs\Election;

use App\DTOs\BaseDataTransferObject;
use App\Enums\ElectionType;

readonly class CreateElectionDto extends BaseDataTransferObject
{
    public function __construct(
        public string $title,
        public int $owner_id,
        public ElectionType $type,
        public bool $quorumRequired,
        public int $mainMemberCount,
        public int $substituteMemberCount,
        public int $incpectorMainMemberCount,
        public int $incpectorSubstituteMemberCount
    ) {}
}
