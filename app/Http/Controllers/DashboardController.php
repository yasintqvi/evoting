<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Group;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (user()->hasRole(Role::Manager->value)) {
            $activities = Activity::latest()->take(50)->get();
        } else {
            $activities = Activity::where("causer_id", user()->id)->take(50)->get();
        }

        if (user()->hasRole(Role::Manager->value)) {
            $groups = Group::all();
        } else {
            $groups = Group::whereHas('users', fn($q) => $q->where('user_id', user()->id))->get();
        }

        return view('app.dashboard', compact('activities', 'groups'));
    }
}
