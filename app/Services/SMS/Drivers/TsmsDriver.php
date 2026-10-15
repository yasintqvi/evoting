<?php

namespace App\Services\SMS\Drivers;

use App\Constants\SMSPattern;
use App\Services\SMS\AbstractSMSDriver;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class TsmsDriver extends AbstractSMSDriver
{
    public function send($phone, $message, $sms_pattern = null)
    {
        foreach (['username', 'password', 'from'] as $key) {
            if (! isset($this->config[$key]) || (string) $this->config[$key] === '') {
                throw new RuntimeException('تنظیمات سرویس پیامک TSMS کامل نیست.');
            }
        }

        $text = $sms_pattern === SMSPattern::OTP
            ? str_replace(':code', (string) $message, $this->config['otp_message'])
            : (string) $message;

        try {
            // Do not retry: a timed-out request may already have sent the SMS.
            $response = Http::connectTimeout(5)->timeout(15)
                ->withOptions(['allow_redirects' => false])
                ->get($this->config['url'], [
                    'from' => $this->config['from'],
                    'to' => $phone,
                    'username' => $this->config['username'],
                    'password' => $this->config['password'],
                    'message' => $text,
                ]);
        } catch (Throwable) {
            // HTTP exceptions can contain the URL, including credentials and OTP.
            throw new RuntimeException('ارتباط با سرویس پیامک برقرار نشد. لطفاً کمی بعد دوباره تلاش کنید.');
        }

        // Observed TSMS send response: a nine-digit message ID. Accept larger IDs
        // as well; reject short status/error codes (e.g. 4), HTML and empty bodies.
        if (! $response->successful() || ! preg_match('/^[1-9][0-9]{8,}$/D', trim($response->body()))) {
            throw new RuntimeException('ارسال کد تأیید توسط سرویس پیامک تأیید نشد.');
        }

        // Verification needs the code itself, not the rendered SMS text.
        $this->save($phone, $message, $sms_pattern);
    }

    public function getDirverName()
    {
        return 'tsms';
    }
}
