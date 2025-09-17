<?php

namespace App\DTOs\ACL;

use App\DTOs\BaseDataTransferObject;

readonly class RoleDto extends BaseDataTransferObject
{
    public function __construct(
        public string $name,
        public array $permissions = []
    ) {}
}
