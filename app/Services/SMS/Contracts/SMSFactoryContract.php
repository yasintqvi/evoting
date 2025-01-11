<?php

namespace App\Services\SMS\Contracts;

interface SMSFactoryContract
{
    /**
     * Summary of driver
     *
     * @param  mixed  $driver
     * @return void
     */
    public function driver($driver = null);
}
