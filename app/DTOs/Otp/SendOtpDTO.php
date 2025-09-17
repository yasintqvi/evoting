<?php

namespace App\DTOs\Otp;


class SendOtpDTO
{
    public function __construct(
        public readonly string $identifier,
    ) {}
}
  