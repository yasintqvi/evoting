<?php

namespace App\Http\Controllers;

use App\Services\Acl\AclService;
use Log;
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

            Log::info('Permissions list retrieved successfully', [
                'performed_by' => auth()->id(),
            ]);

            return view('app.permission.index', compact('permissions'));

        } catch (\Throwable $th) {
            Log::error('Error while retrieving permissions list', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.permission.index_error'));
        }
    }
}
