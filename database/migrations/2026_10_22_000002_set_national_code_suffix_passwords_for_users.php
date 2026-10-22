<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->select('id', 'nationalcode')->whereNull('deleted_at')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $nationalcode = ed($user->nationalcode ?? '');

                    if (! preg_match('/^[0-9]{4,10}$/', $nationalcode ?? '')) {
                        continue;
                    }

                    DB::table('users')->where('id', $user->id)->update([
                        'password' => Hash::make(substr($nationalcode, -4)),
                        'remember_token' => null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Previous password hashes cannot be recovered.
    }
};
