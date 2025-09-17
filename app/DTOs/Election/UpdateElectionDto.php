<?php

namespace App\DTOs\Election;

use App\DTOs\BaseDataTransferObject;

readonly class UpdateElectionDto extends BaseDataTransferObject
{
    public function __construct(
        public string $title,
        public bool $quorumRequired,
        public int $mainMemberCount,
        public int $substituteMemberCount,
        public int $incpectorMainMemberCount,
        public int $incpectorSubstituteMemberCount
    ) {}
}
