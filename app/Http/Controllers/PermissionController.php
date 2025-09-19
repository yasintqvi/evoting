<?php

namespace App\Http\Controllers;

use App\Services\Acl\AclService;
use Throwable;

class PermissionController extends Controller
{
    protected AclService $aclService;

    public function __construct(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    public function __invoke()
    {
        try {
            $permissions = $this->aclService->getAllPermissions();

            return view('app.permission.index', compact('permissions'));
        } catch (Throwable $th) {
            return back()->with('error', __('messages.permission.index_error'));
        }
    }
}
