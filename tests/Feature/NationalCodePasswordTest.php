<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NationalCodePasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'activitylog.enabled' => false]);
        DB::purge('sqlite');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('nationalcode')->nullable();
            $table->string('password')->nullable();
            $table->integer('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function test_user_can_login_with_national_code_suffix_including_leading_zero(): void
    {
        $user = User::create(['phone' => '09123459876', 'nationalcode' => '1234560012']);
        $this->assertTrue(Hash::check('0012', $user->password));
        $this->post('/login', ['identifier' => '۰۹۱۲۳۴۵۹۸۷۶', 'password' => '۰۰۱۲', 'auth_type' => 1])
            ->assertRedirect(route('app.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_national_code_change_updates_default_password(): void
    {
        $user = User::create(['phone' => '09123459876', 'nationalcode' => '1234560012']);
        $user->update(['nationalcode' => '1234560034']);
        $this->assertTrue(Hash::check('0034', $user->fresh()->password));
    }

    public function test_explicit_password_is_preserved(): void
    {
        $user = User::create(['phone' => '09123459876', 'nationalcode' => '1234560012', 'password' => 'custom-password']);
        $this->assertTrue(Hash::check('custom-password', $user->password));
        $user->update(['nationalcode' => '1234560034', 'password' => 'new-password']);
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_phone_change_does_not_change_password(): void
    {
        $user = User::create(['phone' => '09123459876', 'nationalcode' => '۱۲۳۴۵۶۰۰۱۲']);
        $user->update(['phone' => '09123450034']);
        $this->assertTrue(Hash::check('0012', $user->fresh()->password));
        $this->assertFalse(Hash::check('0034', $user->fresh()->password));
    }

    public function test_missing_national_code_does_not_generate_a_mobile_password(): void
    {
        $user = User::create(['phone' => '09123459876']);
        $this->assertNull($user->password);
        $user->update(['nationalcode' => '1234560012']);
        $this->assertTrue(Hash::check('0012', $user->fresh()->password));
    }

    public function test_wrong_password_and_blocked_account_cannot_login(): void
    {
        $user = User::create(['phone' => '09123459876', 'nationalcode' => '1234560012']);
        $this->post('/login', ['identifier' => $user->phone, 'password' => '9999', 'auth_type' => 1])
            ->assertSessionHasErrors('password');
        $this->assertGuest();
        $user->update(['status' => 0]);
        $this->post('/login', ['identifier' => $user->phone, 'password' => '0012', 'auth_type' => 1])
            ->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_migration_updates_existing_passwords_and_clears_remember_tokens(): void
    {
        DB::table('users')->insert(['phone' => '09123459876', 'nationalcode' => '1234560012', 'password' => Hash::make('old-password'), 'remember_token' => 'old-token']);
        $migration = require database_path('migrations/2026_10_22_000002_set_national_code_suffix_passwords_for_users.php');
        $migration->up();
        $user = User::first();
        $this->assertTrue(Hash::check('0012', $user->password));
        $this->assertNull($user->remember_token);
    }
}
