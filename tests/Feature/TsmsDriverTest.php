<?php

namespace Tests\Feature;

use App\Classes\ValidationCode;
use App\Constants\SMSPattern;
use App\Services\SMS\Drivers\TsmsDriver;
use App\Services\SMS\SMSManagerService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class TsmsDriverTest extends TestCase
{
    private function driver(): TsmsDriver
    {
        return new class(app(), ['url' => 'https://www.tsms.ir/url/tsmshttp.php', 'username' => 'test-user', 'password' => 'test$@&password', 'from' => '3000000000', 'otp_message' => 'کد ورود شما: :code']) extends TsmsDriver
        {
            public array $saved = [];

            public function save($phone, $message, $pattern = null)
            {
                $this->saved[] = [$phone, $message, $pattern];
            }
        };
    }

    public function test_sends_otp_to_requested_phone_and_stores_only_the_code(): void
    {
        Http::preventStrayRequests();
        Http::fake(['www.tsms.ir/*' => Http::response('894037823')]);
        $driver = $this->driver();
        $driver->send('09120000000', '01234', SMSPattern::OTP);

        Http::assertSent(function ($request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->method() === 'GET'
                && $query['to'] === '09120000000'
                && $query['from'] === '3000000000'
                && $query['username'] === 'test-user'
                && $query['password'] === 'test$@&password'
                && $query['message'] === 'کد ورود شما: 01234';
        });
        $this->assertSame([['09120000000', '01234', SMSPattern::OTP]], $driver->saved);
    }

    public function test_accepts_changing_message_ids(): void
    {
        foreach (['894037824', '1000000000', " 894037825\n"] as $id) {
            Http::fake(['*' => Http::response($id)]);
            $driver = $this->driver();
            $driver->send('09120000000', '12345', SMSPattern::OTP);
            $this->assertCount(1, $driver->saved);
        }
    }

    public function test_provider_errors_and_unexpected_responses_do_not_store_a_code(): void
    {
        foreach ([['4', 200], ['0', 200], ['-1', 200], ['1', 200], ['', 200], ['<html>Error</html>', 200], ['894037823', 500]] as [$body, $status]) {
            Http::fake(['*' => Http::response($body, $status)]);
            $driver = $this->driver();
            try {
                $driver->send('09120000000', '12345', SMSPattern::OTP);
                $this->fail('Expected send failure');
            } catch (RuntimeException $e) {
                $this->assertSame('ارسال کد تأیید توسط سرویس پیامک تأیید نشد.', $e->getMessage());
                $this->assertSame([], $driver->saved);
            }
        }
    }

    public function test_connection_errors_hide_credentials_and_do_not_store_a_code(): void
    {
        Http::fake(fn () => throw new ConnectionException('URL includes test$@&password'));
        $driver = $this->driver();
        try {
            $driver->send('09120000000', '12345', SMSPattern::OTP);
            $this->fail('Expected connection failure');
        } catch (RuntimeException $e) {
            $this->assertStringNotContainsString('password', $e->getMessage());
            $this->assertNull($e->getPrevious());
            $this->assertSame([], $driver->saved);
        }
    }

    public function test_missing_configuration_does_not_send(): void
    {
        Http::fake();
        $driver = new TsmsDriver(app(), []);
        try {
            $driver->send('09120000000', '12345', SMSPattern::OTP);
            $this->fail('Expected configuration failure');
        } catch (RuntimeException) {
            Http::assertNothingSent();
        }
    }

    public function test_default_driver_is_tsms_and_otp_respects_configured_length(): void
    {
        $this->assertInstanceOf(TsmsDriver::class, app(SMSManagerService::class)->driver());
        config(['auth.constants.code_length' => 5]);
        $this->assertMatchesRegularExpression('/^[0-9]{5}$/', ValidationCode::generate());
    }
}
