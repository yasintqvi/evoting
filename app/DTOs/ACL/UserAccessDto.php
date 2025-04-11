<?php

namespace App\DTOs\ACL;

use App\DTOs\BaseDataTransferObject;

readonly class UserAccessDto extends BaseDataTransferObject
{
    public function __construct(
        public array $permission_ids,
        public array $role_ids
    ) {}
}
