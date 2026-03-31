<?php

namespace App\Services\SMS\Drivers;

use App\Services\SMS\AbstractSMSDriver;
use Exception;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;

class KavenegarDriver extends AbstractSMSDriver
{
    public function send($phone, $message, $sms_pattern = null, $token2 = '', $token3 = '', $token10 = null, $token20 = null)
    {
        try {
            $api = $this->config['api'];
            $lookup_type = $sms_pattern ? $sms_pattern : $this->config['lookup_type'];
            $for_test = $this->config['for_test'] ?? false;

            if ($for_test) {
                $this->save($phone, $this->config['sample']);
            } else {
                $kavenegar = new KavenegarApi($api);
                $kavenegar->VerifyLookup($phone, $message, $token2, $token3, $lookup_type, 'sms', $token10, $token20);

                if (is_null($sms_pattern)) {
                    $this->save($phone, $message, $this->config['lookup_type']);
                } else {
                    $this->save($phone, $message, $sms_pattern);
                }
            }
        } catch (Exception $exception) {
            Log::alert('Error in Kavenegar Driver', [
                'message' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'phone' => $phone,
                'sms_msg' => $message,
                'tokens' => compact(['token2', 'token3', 'token10', 'token20']),
            ]);
        }
    }

    public function call($phone, $token1, $sms_pattern = null, $token2 = '', $token3 = '')
    {
        $api = $this->config['api'];
        $sms_pattern = $sms_pattern ? $sms_pattern : $this->config['lookup_type'];

        $kavenegar = new KavenegarApi($api);

        if ($sms_pattern) {
            $kavenegar->VerifyLookup($phone, $token1, $token2 = '', $token3 = '', $sms_pattern, 'call');
            $this->save($phone, $token1, $sms_pattern);

            return true;
        }

        $kavenegar->CallMakeTTS($phone, $token1);

        if (is_null($sms_pattern)) {
            $this->save($phone, $token1, $this->config['lookup_type']);
        } else {
            $this->save($phone, $token1, $sms_pattern);
        }

        return true;
    }

    public function callTTS(string $phone, string $message, $date = null, $localid = null)
    {
        $api = $this->config['api'];
        $kavenegar = new KavenegarApi($api);

        $kavenegar->CallMakeTTS($phone, $message, $date, $localid);

        $this->save($phone, $message, $date);

        return true;
    }
}
