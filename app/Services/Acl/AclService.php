<?php

namespace App\Services\Acl;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use App\Enums\Role as RoleEnum;
use App\DTOs\ACL\RoleDto;
use App\DTOs\ACL\UserAccessDto;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class AclService
{
    public function getAllRoles(): Collection
    {
        try {
            return Role::all();
        } catch (Exception $e) {
            Log::error('Failed to get all roles', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getAllPermissions(): Collection
    {
        try {
            return Permission::all();
        } catch (Exception $e) {
            Log::error('Failed to get all permissions', [
                'error' => $e->getMessage()
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
                'error' => $e->getMessage()
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
                'role_name' => $role->name
            ]);

            return $role;
        } catch (Exception $e) {
            Log::error('Failed to create role', [
                'role_name' => $roleDto->name,
                'error' => $e->getMessage()
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
                'role_name' => $role->name
            ]);

            return $role;
        } catch (Exception $e) {
            Log::error('Failed to update role', [
                'role_id' => $role->id,
                'role_name' => $roleDto->name,
                'error' => $e->getMessage()
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
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteRole(Role $role): bool
    {
        try {
            if (in_array($role->name, [RoleEnum::Manager])) {
                Log::warning('Attempted to delete manager role', [
                    'role_id' => $role->id
                ]);
                return false;
            }

            if (!request()->user()->hasRole(RoleEnum::Manager)) {
                Log::warning('Non-manager user attempted to delete role', [
                    'user_id' => request()->user()->id,
                    'role_id' => $role->id
                ]);
                return false;
            }

            $role->delete();

            Log::info('Role deleted successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete role', [
                'role_id' => $role->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
