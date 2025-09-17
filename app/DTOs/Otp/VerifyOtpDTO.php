<?php

namespace App\DTOs\Otp;

class VerifyOtpDTO
{
    public function __construct(
        public readonly string $otp,
    ) {}
}
