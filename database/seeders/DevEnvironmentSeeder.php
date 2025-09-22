<?php

namespace Database\Seeders;

use App\Enums\GroupStatus;
use App\Enums\Permission;
use App\Enums\Role;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role as ModelsRole;

class DevEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'first_name' => 'مدیر سایت',
            'phone' => '09931623277',
            'password' => bcrypt('12345678'),
        ]);

        $testGroup = Group::create([
            'title' => 'Evoting Test Group',
            'description' => 'this is the test group in the evoting system',
            'owner_id' => $adminUser->id,
            'status' => GroupStatus::ENABLE,
            'logo' => 'assets/img/group.jpg',
        ]);

        $testGroup->users()->attach($adminUser->id);

        foreach (Role::cases() as $role) {
            $roleModel = ModelsRole::create(['name' => $role->value]);
            $permissions = Permission::getPermissionsByRole($role);

            foreach ($permissions as $permission) {
                $permissionModel = ModelsPermission::firstOrCreate([
                    'name' => $permission->value,
                ]);
                $roleModel->givePermissionTo($permissionModel);
            }
        }

        $adminUser->assignRole('admin');
        $adminUser->givePermissionTo(ModelsPermission::all());
    }
}
