<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\PositionType;
use App\Enums\Role;
use App\Models\Position;
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
        // create admin user
        $adminUser = User::factory()->create([
            'first_name' => 'مدیر سایت',
            'phone' => '09931623277',
            'password' => bcrypt('12345678'),
        ]);

        foreach (Role::cases() as $role) {
            $roleModel = ModelsRole::firstOrCreate(['name' => $role->value]);
            $permissions = Permission::getPermissionsByRole($role);

            foreach ($permissions as $permission) {
                $permissionModel = ModelsPermission::firstOrCreate([
                    'name' => $permission->value,
                ]);
                $roleModel->syncPermissions($permissionModel);
            }
        }

        $adminUser->assignRole('admin');
        $adminUser->givePermissionTo(ModelsPermission::all());

        // create default positions

        foreach (PositionType::cases() as $position) {
            Position::firstOrCreate([
                'title' => $position->label(),
            ]);
        }
    }
}
