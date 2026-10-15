<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class OtpSendRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->softDeletes();
            $table->integer('two_factor_type')->default(1);
        });
        Schema::create('short_messages', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->string('content');
            $table->string('handler');
            $table->string('pattern')->nullable();
            $table->text('meta')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        Http::preventStrayRequests();
        Http::fake(['*' => Http::response('894037823')]);
        config(['sms.drivers.tsms.username' => 'test', 'sms.drivers.tsms.password' => 'test', 'sms.drivers.tsms.from' => '3000000']);
    }

    public function test_unknown_phone_returns_visible_json_error_without_sending(): void
    {
        $this->postJson('/otp/send', ['identifier' => '09115029577'])
            ->assertNotFound()->assertJson(['message' => 'کاربری با این شماره ثبت نشده است.']);
        Http::assertNothingSent();
    }

    public function test_registered_phone_with_persian_digits_sends_and_stores_otp(): void
    {
        DB::table('users')->insert(['phone' => '09115029577', 'two_factor_type' => 1]);
        $this->postJson('/otp/send', ['identifier' => '۰۹۱۱۵۰۲۹۵۷۷'])
            ->assertOk()->assertJson(['identifier' => '09115029577', 'two_factor_type' => 1]);
        Http::assertSentCount(1);
        $message = DB::table('short_messages')->first();
        $this->assertSame('09115029577', $message->to);
        $this->assertMatchesRegularExpression('/^[0-9]{5}$/', $message->content);
    }
}
