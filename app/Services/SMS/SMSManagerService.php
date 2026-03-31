<?php

namespace App\Services\SMS;

use App\Services\SMS\Contracts\SMSFactoryContract;
use App\Services\SMS\Drivers\KavenegarDriver;
use Illuminate\Support\Manager;

/**
 * SMSManager class
 */
class SMSManagerService extends Manager implements SMSFactoryContract
{
    public const CONFIG_FILE_NAME = 'sms';

    public const KAVENEGAR = 'KAVENEGAR';

    /**
     * Get all the available "drivers".
     *
     * @return array
     */
    public static function availableDrivers()
    {
        return [
            self::KAVENEGAR,
        ];
    }

    public function getDefaultDriver()
    {
        return self::KAVENEGAR;
    }

    protected function createKavenegarDriver()
    {
        $config = $this->container['config'][self::CONFIG_FILE_NAME.'.drivers.kavenegar'];

        return $this->buildDriver(KavenegarDriver::class, $config);
    }

    /**
     * Build an SMS driver instance.
     *
     * @param  string  $driver
     * @param  array  $config
     * @return \App\Services\SMS\AbstractSmsDriver
     */
    public function buildDriver($driver, $config)
    {
        return new $driver($this->container, $config);
    }
}
