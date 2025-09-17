<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class UserActivityController extends Controller
{
    public function __invoke(Request $request)
    {

        $activities = Activity::query();

        if ($request->has('user_id') && !empty($request->get('user_id'))) {
            $activities->where('causer_id', $request->get('user_id'));
        }

        $activities = $activities->latest()->paginate(100);

        $users = User::all();

        return view('app.users.user-activities', compact('activities', 'users'));
    }
}
