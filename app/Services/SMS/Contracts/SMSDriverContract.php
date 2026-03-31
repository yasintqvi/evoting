<?php

namespace App\Services\SMS\Contracts;

interface SMSDriverContract
{
    /**
     * Summary of send
     *
     * @param  mixed  $phone
     * @param  mixed  $message
     * @param  mixed  $sms_pattern
     * @return void
     */
    public function send($phone, $message, $sms_pattern = null);
}
