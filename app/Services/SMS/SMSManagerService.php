<?php

namespace App\Services\SMS;

use App\Services\SMS\Contracts\SMSFactoryContract;
use App\Services\SMS\Drivers\TsmsDriver;
use Illuminate\Support\Manager;

/**
 * SMSManager class
 */
class SMSManagerService extends Manager implements SMSFactoryContract
{
    public const CONFIG_FILE_NAME = 'sms';

    public const TSMS = 'tsms';

    /**
     * Get all the available "drivers".
     *
     * @return array
     */
    public static function availableDrivers()
    {
        return [
            self::TSMS,
        ];
    }

    public function getDefaultDriver()
    {
        return self::TSMS;
    }

    protected function createTsmsDriver()
    {
        $config = $this->container['config'][self::CONFIG_FILE_NAME.'.drivers.tsms'];

        return $this->buildDriver(TsmsDriver::class, $config);
    }

    /**
     * Build an SMS driver instance.
     *
     * @param  string  $driver
     * @param  array  $config
     * @return AbstractSmsDriver
     */
    public function buildDriver($driver, $config)
    {
        return new $driver($this->container, $config);
    }
}
