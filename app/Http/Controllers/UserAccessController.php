<?php

namespace App\Http\Controllers;

use App\Http\Requests\ACL\UserAccessRequest;
use App\Models\User;
use App\Services\Acl\AclService;
use Illuminate\Http\Request;
use Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class UserAccessController extends Controller
{
    private AclService $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    public function edit(User $user)
    {
        $permissions = Permission::all();

        $roles = Role::all();

        return view('app.users.edit-access', compact('user', 'permissions', 'roles'));
    }

    /**
     * Handle the incoming request.
     */
    public function update(UserAccessRequest $request, User $user)
    {
        try {
            $this->aclService->updateUserAccess($user, $request->toDto());

            return to_route('users.index')->with('success', __('messages.user.access_changed'));
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
