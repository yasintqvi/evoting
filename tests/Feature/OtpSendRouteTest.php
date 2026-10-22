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
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'activitylog.enabled' => false]);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->softDeletes();
            $table->integer('two_factor_type')->default(1);
            $table->integer('status')->default(1);
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
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

    public function test_active_code_returns_remaining_wait_without_sending_another_sms(): void
    {
        DB::table('users')->insert(['phone' => '09115029577']);
        $this->postJson('/otp/send', ['identifier' => '09115029577'])->assertOk();
        $this->postJson('/otp/send', ['identifier' => '09115029577'])
            ->assertStatus(429)->assertJson(['code' => 'active_otp', 'identifier' => '09115029577'])
            ->assertJsonPath('retry_after', fn ($value) => $value > 0 && $value <= 180);
        Http::assertSentCount(1);
    }

    public function test_valid_otp_logs_in_and_honors_remember_me(): void
    {
        DB::table('users')->insert(['phone' => '09115029577']);
        $this->postJson('/otp/send', ['identifier' => '09115029577'])->assertOk();
        $code = DB::table('short_messages')->value('content');
        $this->post('/login', ['identifier' => '09115029577', 'auth_type' => 2, 'otp' => $code, 'remember' => 1])
            ->assertRedirect(route('app.index'))->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $this->assertNotNull(DB::table('users')->value('remember_token'));
    }

    public function test_invalid_otp_preserves_phone_and_method_but_not_code(): void
    {
        DB::table('users')->insert(['phone' => '09115029577']);
        $this->post('/login', ['identifier' => '09115029577', 'auth_type' => 2, 'otp' => '00000'])
            ->assertRedirect(route('otp.form'))->assertSessionHasErrors('otp')
            ->assertSessionHas('_old_input.identifier', '09115029577')
            ->assertSessionHas('_old_input.auth_type', 2)->assertSessionMissing('_old_input.otp');
        $this->assertGuest();
    }

    public function test_both_login_pages_render_the_shared_form(): void
    {
        $this->get('/login')->assertOk()->assertSee('data-initial-mode="password"', false);
        $this->get('/login/otp')->assertOk()->assertSee('data-initial-mode="otp"', false);
    }
}
