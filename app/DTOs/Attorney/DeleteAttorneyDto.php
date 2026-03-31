<?php

namespace App\DTOs\Attorney;

use App\DTOs\BaseDataTransferObject;
use App\Models\Participant;

readonly class DeleteAttorneyDto extends BaseDataTransferObject
{
    public function __construct(
        public Participant $participant
    ) {}
}
