<?php

namespace App\Services\Acl;

use App\DTOs\ACL\RoleDto;
use App\DTOs\ACL\UserAccessDto;
use App\Enums\Role as RoleEnum;
use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class AclService
{
    public function getAllRoles(): Collection
    {
        try {
            return Role::where('group_id', null)->get();
        } catch (Exception $e) {
            Log::error('Failed to get all roles', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getRoleGroup(Group $group)
    {
        try {
            return Role::where('group_id', $group->id)->get();
        } catch (Exception $e) {
            Log::error('Failed to get all roles', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getAllPermissions(): Collection
    {
        try {
            return Permission::where('group_id',null)->get();
        } catch (Exception $e) {
            Log::error('Failed to get all permissions', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getGroupPermissions(Group $group)
    {
        try {
            return Permission::where('group_id', $group->id)->get();
        } catch (Exception $e) {
            Log::error('Failed to get all permissions', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function getRolePermissions(Role $role): array
    {
        try {
            return $role->permissions->pluck('id')->toArray();
        } catch (Exception $e) {
            Log::error('Failed to get role permissions', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function createRole(RoleDto $roleDto): Role
    {
        try {
            $role = Role::create(['name' => $roleDto->name, 'guard_name' => 'web']);

            if (!empty($roleDto->permissions)) {
                $permissions = Permission::whereIn('id', $roleDto->permissions)->get();
                $role->syncPermissions($permissions);
            }

            Log::info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            return $role;
        } catch (Exception $e) {
            Log::error('Failed to create role', [
                'role_name' => $roleDto->name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function updateRole(Role $role, RoleDto $roleDto): Role
    {
        try {
            $role->update(['name' => $roleDto->name]);

            if (!empty($roleDto->permissions)) {
                $permissions = Permission::whereIn('id', $roleDto->permissions)->get();
                $role->syncPermissions($permissions);
            }

            Log::info('Role updated successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            return $role;
        } catch (Exception $e) {
            Log::error('Failed to update role', [
                'role_id' => $role->id,
                'role_name' => $roleDto->name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function updateUserAccess(User $user, UserAccessDto $userAccessDto)
    {
        try {
            $user->permissions()->sync($userAccessDto->permission_ids);

            $user->roles()->sync($userAccessDto->role_ids);
        } catch (Throwable $e) {
            Log::error('Failed to update role', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function updateGroupUserAccess(User $user, UserAccessDto $userAccessDto, Group $group, $otherPermissions = null)
    {
        try {
            //this code is for not sync other roles group or other
            //main roles and permission here just for group
            $allGroupPermissions = Permission::where('group_id', $group->id)->get();
            $allGroupRoles = Role::where("group_id", $group->id)->pluck('id')->toArray();
            $user->revokePermissionTo($allGroupPermissions);


            $collectPermissions = [];
            if ($otherPermissions) {
                $otherPermissions = array_unique($otherPermissions);
                $permissionsRecord = array_values($otherPermissions);

                $permissionsRecord = array_map(function ($permission) use ($group) {
                    return [
                        'name' => $permission,
                        'guard_name' => 'web',
                        'group_id' => $group->id
                    ];
                }, $permissionsRecord);

                if (!empty($permissionsRecord)) {
                    // Create permissions if they don't exist, and collect the models
                    foreach ($permissionsRecord as $permission) {
                        $model = Permission::firstOrCreate(['name' => $permission['name']], $permission);
                        $collectPermissions[] = $model;
                    }
                }
            }

            $permissionIds = array_merge($userAccessDto->permission_ids, collect($collectPermissions)->pluck('id')->toArray());

            $permissions = Permission::whereIn('id', $permissionIds)->get();

            $user->givePermissionTo($permissions);


            $user->roles()->detach($allGroupRoles);
            $user->roles()->attach($userAccessDto->role_ids);
        } catch (Throwable $e) {
            Log::error('Failed to update role', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function deleteRole(Role $role): bool
    {
        try {
            if (in_array($role->name, [RoleEnum::Manager])) {
                Log::warning('Attempted to delete manager role', [
                    'role_id' => $role->id,
                ]);
                return false;
            }
            if (!request()->user()->hasRole(RoleEnum::Manager)) {
                Log::warning('Non-manager user attempted to delete role', [
                    'user_id' => request()->user()->id,
                    'role_id' => $role->id,
                ]);

                return false;
            }

            $role->delete();

            Log::info('Role deleted successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete role', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a new role for a specific group with given permissions.
     *
     * @param string $name The name of the role to create.
     * @param array $permissionsRecord An array of permission names to create (as strings).
     * @param array $permissions An array of existing permission IDs to assign to the role.
     * @param Group $group The group instance to which this role belongs.
     *
     * @return Role                      The created Role model instance.
     *
     * @throws Exception                 Throws exception if role creation fails.
     *
     * Steps:
     * 1. Begin a database transaction to ensure atomicity.
     * 2. Create a new Role record with the given name and associate it with the group.
     * 3. Convert each permission string in $permissionsRecord into an array with keys:
     *      - 'name'       => permission name
     *      - 'guard_name' => 'web' (default guard)
     *      - 'group_id'   => associated group ID
     * 4. For each permission in $permissionsRecord:
     *      - Use firstOrCreate to avoid creating duplicates.
     *      - Collect the Permission model instances in $collectPermissions array.
     * 5. Retrieve any existing permissions by their IDs ($permissions) and add them to $collectPermissions.
     * 6. Assign all collected permissions to the newly created role using syncPermissions().
     * 7. Commit the transaction if everything succeeds.
     * 8. Log role creation success.
     * 9. If an exception occurs:
     *      - Roll back the transaction.
     *      - Log the error.
     *      - Rethrow the exception.
     */
    public function createGroupRole(string $name, array $permissionsRecord, array $permissions, Group $group)
    {
        try {
            DB::beginTransaction();

            // Create the role
            $role = Role::create([
                'name' => $name,
                'guard_name' => 'web',
                'group_id' => $group->id
            ]);

            //delete any duplicate permission for creating it
            $permissionsRecord = array_unique($permissionsRecord);
            $permissionsRecord = array_values($permissionsRecord);
            // Convert permission names to arrays for firstOrCreate
            $permissionsRecord = array_map(function ($permission) use ($group) {
                return [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_id' => $group->id
                ];
            }, $permissionsRecord);

            if (!empty($permissionsRecord)) {
                $collectPermissions = [];

                // Create permissions if they don't exist, and collect the models
                foreach ($permissionsRecord as $permission) {
                    $model = Permission::firstOrCreate(['name' => $permission['name']], $permission);
                    $collectPermissions[] = $model;
                }

                // Add existing permissions by IDs to collection
                $collectPermissions = array_merge($collectPermissions, Permission::whereIn('id', $permissions)->get()->all());

                // Assign all permissions to the role
                $role->syncPermissions($collectPermissions);
            }

            Log::info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            DB::commit();
            return $role;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to create role', [
                'role_name' => $name,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function updateGroupRole(Role $role, string $name, array $permissionsRecord, array $permissions, Group $group): Role
    {
        try {
            DB::beginTransaction();
            $role->update(['name' => $name]);

            $permissionsRecord = array_unique($permissionsRecord);
            $permissionsRecord = array_values($permissionsRecord);
            // Convert permission names to arrays for firstOrCreate
            $permissionsRecord = array_map(function ($permission) use ($group) {
                return [
                    'name' => $permission,
                    'guard_name' => 'web',
                    'group_id' => $group->id
                ];
            }, $permissionsRecord);

            if (!empty($permissionsRecord)) {
                $collectPermissions = [];

                // Create permissions if they don't exist, and collect the models
                foreach ($permissionsRecord as $permission) {
                    $model = Permission::firstOrCreate(['name' => $permission['name']], $permission);
                    $collectPermissions[] = $model;
                }

                // Add existing permissions by IDs to collection
                $collectPermissions = array_merge($collectPermissions, Permission::whereIn('id', $permissions)->get()->all());

                // Assign all permissions to the role
                $role->syncPermissions($collectPermissions);
            }
            Log::info('Role updated successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name,
            ]);

            DB::commit();
            return $role;
        } catch
        (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update role', [
                'role_id' => $role->id,
                'role_name' => $name,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }


}
