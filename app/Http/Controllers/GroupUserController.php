<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group )
    {
        $group = $group->load('users');
        return view('app.group.users.index' , compact('group'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group )
    {
        $users = User::whereDoesntHave('groups', function ($query) use ($group){
            $query->where('group_id' , $group->id);
        })->get();

        return view('app.group.users.create' , compact('group' , 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Group $group)
    {
        if ($request->has('phone'))
        {
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|unique:users,phone',
            ]);

            $user = User::create($validatedData);

            $group->users()->attach($user->id);

            return redirect()->route('election-users.index', $group->slug)
            ->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه اضافه شد.');
        }
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
