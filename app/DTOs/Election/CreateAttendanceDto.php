<?php

namespace App\DTOs\Election;

use App\DTOs\BaseDataTransferObject;

readonly class CreateAttendanceDto extends BaseDataTransferObject
{
    public function __construct(
        public array $participantsAttendance,
        public array $participantsAttorney
    ) {}
}
