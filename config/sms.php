<?php

use App\Constants\SMSPattern;

return [
    'drivers' => [
        'kavenegar' => [
            'lookup_type' => 'SendLogin',
            'api' => env('KAVENEGAR_API_KEY'),
            'for_test' => config('app.env') === 'testing',
            'sample' => 12345,
            'patterns' => [
                SMSPattern::OTP,
            ],
            'messages' => [],
        ],
    ],
];
