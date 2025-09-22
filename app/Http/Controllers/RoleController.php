<?php

namespace App\Http\Controllers;

use App\Http\Requests\ACL\RoleRequest;
use App\Services\Acl\AclService;
use Spatie\Permission\Models\Role as ModelsRole;
use Throwable;

class RoleController extends Controller
{
    public function __construct(protected AclService $aclService) {}

    public function index()
    {
        $roles = $this->aclService->getAllRoles();

        return view('app.role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->aclService->getAllPermissions();

        return view('app.role.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        try {
            $this->aclService->createRole($request->toDto());

            return to_route('roles.index')->with('success', __('messages.role.created'));
        } catch (Throwable $th) {

            return back()->with('error', __('messages.role.create_error'));
        }
    }

    public function edit(ModelsRole $role)
    {
        $permissions = $this->aclService->getAllPermissions();

        $rolePermissions = $this->aclService->getRolePermissions($role);

        return view('app.role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(RoleRequest $request, ModelsRole $role)
    {
        try {
            $this->aclService->updateRole($role, $request->toDto());

            return to_route('roles.index')->with('success', __('messages.role.updated'));
        } catch (Throwable $th) {

            return back()->with('error', __('messages.role.update_error'));
        }
    }

    public function destroy(ModelsRole $role)
    {
        try {
            if (! $this->aclService->deleteRole($role)) {

                return back()->with('error', __('messages.role.cannot_delete_system'));
            }

            return back()->with('success', __('messages.role.deleted'));
        } catch (Throwable $th) {

            return back()->with('error', __('messages.role.delete_error'));
        }
    }
}
