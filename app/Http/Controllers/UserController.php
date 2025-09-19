<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"]);
                });
            })
            ->latest()
            ->get();

        return view('app.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::all();
        $users = User::all();

        return view('app.users.create', compact('groups', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if ($request->has('phone')) {
            $request->mergeIfMissing(['is_active' => 0]);
            $inputs = $request->except('group_id');
            $user = User::create($inputs);

            $groupId = $request->input('group_ids');
            $user->groups()->sync($groupId);
        } else {

            foreach ($request->group_ids as $groupId) {
                $group = Group::find($groupId);
                $group->users()->syncWithoutDetaching($request->user_ids);
            }
        }

        return redirect()->back()->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه‌های انتخابی اضافه شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, Group $group)
    {
        $groups = $group->get();

        return view('app.users.edit', compact('user', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $request->mergeIfMissing(['is_active' => 0]);
        $inputs = $request->except('group_ids');
        $user->update($inputs);

        $groupIds = $request->input('group_ids', []);
        $user->groups()->sync($groupIds);

        return redirect()->route('users.index')->with('success', 'اطلاعات کاربر با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
