<?php

return [
    'drivers' => [
        'tsms' => [
            'url' => env('TSMS_URL', 'https://www.tsms.ir/url/tsmshttp.php'),
            'username' => env('TSMS_USERNAME'),
            'password' => env('TSMS_PASSWORD'),
            'from' => env('TSMS_FROM'),
            'otp_message' => env('TSMS_OTP_MESSAGE', 'کد ورود شما: :code'),
        ],
    ],
];
