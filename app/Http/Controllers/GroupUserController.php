<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {
        $search = request('search');

        $group->load(['users' => function ($query) use ($search) {
            if ($search) {
                $query->where(
                    fn($q) =>
                    $q->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                );
            }

            $query->latest();
        }]);

        return view('app.group.users.index', compact('group'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group)
    {

        $users = User::whereDoesntHave('groups', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->get();

        $stockWeight = $group->prefered_stock_weight;

        return view('app.group.users.create', compact('group', 'users', 'stockWeight'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupUserRequest $request, Group $group)
    {
        $phone = convert_persian_to_english($request->input('phone'));
        $nationalcode = convert_persian_to_english($request->input('nationalcode'));

        $userData = $request->validated();
        $userData['phone'] = $phone;
        $userData['nationalcode'] = $nationalcode;
        $userData['password'] = Hash::make($nationalcode);

        $user = User::create($userData);

        $pivotData = [];

        if ($group->type == \App\Enums\GroupType::SPECIAL) {
            $pivotData = [
                'normal_stock_count' => $request->input('normal_stock_count', 0),
                'prefered_stock_count' => $request->input('prefered_stock_count', 0),
            ];
        }
        $group->users()->attach($user->id, $pivotData);

        return redirect()->route('group.users.index', $group->slug)
            ->with('success', 'کاربر جدید با موفقیت ایجاد و به گروه اضافه شد.');
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
        $userStock = $group->users()->where('user_id', $user->id)->first()->pivot;
        return view('app.group.users.edit', compact('group', 'user', 'userStock'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupUserRequest $request, Group $group, User $user)
    {
        $nationalcode = convert_persian_to_english($request->input('nationalcode'));

        $userData = $request->validated();
        $userData['nationalcode'] = $nationalcode;

        if ($user->nationalcode !== $nationalcode) {
            $userData['password'] = Hash::make($nationalcode);
        }

        $user->update($userData);

        if ($group->type == \App\Enums\GroupType::SPECIAL) {
            $pivotData = [
                'normal_stock_count' => $request->input('normal_stock_count', 0),
                'prefered_stock_count' => $request->input('prefered_stock_count', 0),
            ];
        }
        $group->users()->updateExistingPivot($user->id, $pivotData);

        return redirect()->route('group.users.index', $group->slug)
            ->with('success', __('messages.company_user_update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, User $user)
    {
        $group->users()->detach($user);
        return back()->with('success', __('messages.company_user_delete'));
    }
}
