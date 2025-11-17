<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\Request;

class ModulePermissionController extends Controller
{
    public function __invoke(string $module, Group $group)
    {
        $data = null;
        switch ($module) {
            case ($module == 'events'):
                $data = $group->events;
                break;
            case ($module == 'elections'):
                $data = $group->elections;
                break;
            case($module == "surveys"):
                $data = $group->surveys;
                break;
        }

        return response()->json($data);
    }
}
