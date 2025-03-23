<?php

namespace App\DTOs\Participant;

use App\DTOs\BaseDataTransferObject;

readonly class CreateParticipantDto extends BaseDataTransferObject
{
    public function __construct(
        public int $election_id,
        public int $user_id,
        public int $normal_stock_count,
        public int $prefered_stock_count,
        public bool $is_present,
    ) {}
}
