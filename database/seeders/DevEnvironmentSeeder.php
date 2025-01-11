<?php

namespace Database\Seeders;

use App\Enums\GroupStatus;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'first_name' => 'مدیر سایت',
            'phone' => "09931623277",
            'password' => bcrypt('12345678')
        ]);

        User::factory(10)->create();

        $testGroup = Group::create([
            'title' => 'Evoting Test Group',
            'description' => 'this is the test group in the evoting system',
            'owner_id' => $adminUser->id,
            'status' => GroupStatus::ENABLE
        ]);
        
        $users = User::factory(10)->create();

        $testGroup->users()->sync($users);   
    }
}
