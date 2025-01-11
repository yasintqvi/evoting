<?php

namespace App\Facades;

use App\Services\SMS\Contracts\SMSFactoryContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool call($phone, $token1, $sms_pattern = null, $token2 = '', $token3 = '')
 * @method static void send($phone, $message, $sms_pattern = null, $token2 = '', $token3 = '',$token10 = null,$token20 = null)
 * @method static void callTTS(string $phone, string $message, $date = null, $localid = null)
 *
 * @see \Modules\SMS\Managers\SMSManager
 */
class SMSFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SMSFactoryContract::class;
    }
}
