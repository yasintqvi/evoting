<?php

namespace App\DTOs\Auth;


class LoginDTO
{
    public function __construct(
        public readonly string $identifier,
        public readonly int $auth_type,
        public readonly ?string $password = null,
        public readonly ?string $otp = null,
        public readonly bool $remember = false
    ) {}
}
