<?php

namespace App\DTOs\Attorney;

use App\DTOs\BaseDataTransferObject;
use App\Models\Participant;
use App\Models\User;

readonly class CreateAttorneyDto extends BaseDataTransferObject
{
    public function __construct(
        public User $user,
        public Participant $participant
    ) {}
}
