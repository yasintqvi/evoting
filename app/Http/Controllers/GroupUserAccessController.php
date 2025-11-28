<?php

namespace App\Http\Controllers;

use App\Http\Requests\ACL\UserAccessRequest;
use App\Models\Group;
use App\Models\User;
use App\Services\Acl\AclService;
use Illuminate\Http\Request;
use Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class GroupUserAccessController extends Controller
{
    private AclService $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    public function edit(Group $group, User $user)
    {
        $roles = $user->roles;
        $permissions = $this->aclService->getGroupPermissions($group);
        $otherPermissions = [];
        $otherRolePermissions = [];
        $rolePermissions = [];
        foreach ($permissions as $perm) {
            if (preg_match('/_group_\d+$/', $perm->name)) {
                $groupPermissions[] = $perm; // ends with group_<number>
            }
        }
        $permissions = $groupPermissions;
        foreach ($roles as $role) {
            $rolePermissions += $this->aclService->getRolePermissions($role);
        }

        $otherRolePermissions[] =$user->getGroupPermissions($group);
        foreach ($otherRolePermissions as $perm) {
            foreach ($perm as $subperm) {
                if (!preg_match('/_group_\d+$/', $subperm->name)) {
                    $otherPermissions[] = $subperm; // ends with group_<number>
                }
            }
        }

        $roles = Role::where('group_id', $group->id)->get();

        return view('app.group.users.edit-access', compact('user', 'permissions', 'roles', 'group', 'otherPermissions', 'rolePermissions'));
    }

    /**
     * Handle the incoming request.
     */
    public function update(UserAccessRequest $request,Group $group, User $user)
    {
        try {
            $otherPermissions=$request->input('permissionsRecord');
            $this->aclService->updateGroupUserAccess($user, $request->toDto(),$group,$otherPermissions);

            return back()->with('success', __('messages.user.access_changed'));
        } catch (Throwable $th) {
            Log::error('Error updating user access', [
                'performed_by' => user()->id ?? null,
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.user.user_access_error'));
        }
    }
}
