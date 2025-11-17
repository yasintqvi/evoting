<?php

namespace App\Http\Controllers;

use App\Http\Requests\ACL\RoleRequest;
use App\Models\Group;
use App\Services\Acl\AclService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class GroupPermissionController extends Controller
{
    public function __construct(protected AclService $aclService)
    {
    }

    public function index(Group $group)
    {
        $roles = $this->aclService->getRoleGroup($group);

        return view('app.group.groupRole.index', compact('roles', 'group'));
    }

    public function create(Group $group)
    {
        $permissions = $this->aclService->getGroupPermissions($group);

        return view('app.group.groupRole.create', compact('permissions', 'group'));
    }

    public function store(Request $request, Group $group)
    {
        try {
            $data = $request->validate([
                'name' => 'string|required',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id',
                'permissionsRecord' => 'array',
            ]);

            $permissions = $data['permissions'];
            $name = $data['name'];
            $permissionsRecord = $data['permissionsRecord'];


            $this->aclService->createGroupRole($name, $permissionsRecord, $permissions, $group);

            return to_route('group.permissions')->with('success', __('messages.role.created'));
        } catch (Throwable $th) {
            Log::error('Error creating role', [
                'user_id' => user()->id ?? null,
                'request_data' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.role.create_error'));
        }
    }

    public function edit(Group $group, Role $role)
    {
        $permissions = $this->aclService->getGroupPermissions($group);
        $otherPermissions = [];
        foreach ($permissions as $perm) {
            if (preg_match('/_group_\d+$/', $perm->name)) {
                $groupPermissions[] = $perm; // ends with group_<number>
            }
        }
        $permissions = $groupPermissions;
        $rolePermissions = $this->aclService->getRolePermissions($role);
        $otherRolePermissions = $role->permissions;
        foreach ($otherRolePermissions as $perm) {
            if (!preg_match('/_group_\d+$/', $perm->name)) {
                $otherPermissions[] = $perm; // ends with group_<number>
            }
        }

        return view('app.group.groupRole.edit', compact('role', 'permissions', 'rolePermissions', 'otherPermissions', 'group'));
    }

    public function update(Request $request, Group $group, Role $role)
    {
        try {
            $data = $request->validate([
                'name' => 'string|required',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id',
                'permissionsRecord' => 'array',
            ]);
            $permissions = $data['permissions'];
            $name = $data['name'];
            $permissionsRecord = $data['permissionsRecord'];


            $this->aclService->updateGroupRole($role, $name, $permissionsRecord, $permissions, $group);

            return to_route('group.permissions', $group)->with('success', __('messages.role.updated'));

        } catch (Throwable $th) {
            Log::error('Error updating role', [
                'user_id' => user()->id ?? null,
                'role_id' => $role->id,
                'request_data' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.role.update_error'));
        }
    }

    public function destroy(Group $group, Role $role)
    {
        DB::beginTransaction();
        try {
            if ($role->group_id == $group->id) {
                $role->permissions()->detach();
                $role->delete();

                return back()->with('success', __('messages.role.deleted'));
            }

            return back()->with('error', __('messages.role.delete_error'));
        } catch (\Throwable $th) {
                Log::error('Error deleting role', [
                'user_id' => user()->id ?? null,
                'role_id' => $role->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
            return back()->with('error', __('messages.role.delete_error'));

        }
    }


}
