<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Election;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        $group = $group->load(['users' => function ($query) {
            $query->latest();
        }]);

        return view('app.group.users.index' , compact('group' ));
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
    public function store(StoreGroupUserRequest $request, Group $group)
    {
        if ($request->has('phone'))
        {
            $phone = convert_persian_to_english($request->input('phone'));
            
            $userData = $request->validated();
            $userData['phone'] = $phone;        

            $user = User::create($userData);

            $group->users()->attach($user->id);

            return redirect()->route('group.users.index', $group->slug)
            ->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه اضافه شد.');
        }
        else {
            $group->users()->syncWithoutDetaching($request->input('user_ids'));

            return redirect()->route('group.users.index', $group->slug)
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
    public function edit(Group $group, User $user)
    {
        return view('app.group.users.edit', compact('group','user'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupUserRequest  $request, Group $group, User $user)
    {
        $userData = $request->validated();
        $user->update($userData);

        return redirect()->route('group.users.index', $group->slug)
            ->with('success', 'ویرایش جدید با موفقیت ایجاد و به گروه اضافه شد.');
    }
    

    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
