<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {

        $search = request('search');

        $group->load([
            'users' => function ($query) use ($search) {
                if ($search) {
                    $query->where(
                        fn($q) =>
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    );
                }

                $query->latest();
            }
        ]);

        $users = $group->users;


        return view('app.group.users.index', compact('group', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group)
    {
        $search = request('search');

        $users = User::query()
            ->whereDoesntHave('groups', function ($q) use ($group) {
                $q->where('groups.id', $group->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            })
            ->latest()
            ->get();

        return view('app.group.users.create', compact('group', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->users()->attach($validated['user_ids']);

        return redirect()->back()->with('success', 'کاربران با موفقیت اضافه شدند.');
    }

    public function createParticipant(Group $group)
    {
        $search = request('search');

        $group->load([
            'users' => function ($query) use ($search) {
                if ($search) {
                    $query->where(
                        fn($q) =>
                        $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    );
                }

                $query->latest();
            }
        ]);

        $users = $group->users;

        return view('app.group.users.particpent', compact('group', 'users'));
    }

    public function storeParticipant(Request $request, Group $group)
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*.normal_stock_count' => 'required|integer|min:0',
            'users.*.prefered_stock_count' => 'required|integer|min:0',
        ]);

        foreach ($validated['users'] as $userId => $stockData) {
            $group->users()->syncWithoutDetaching([
                $userId => [
                    'normal_stock_count' => $stockData['normal_stock_count'],
                    'prefered_stock_count' => $stockData['prefered_stock_count'],
                ]
            ]);
        }

        return redirect()->back()->with('success', 'کاربران با موفقیت به گروه اضافه شدند.');
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
    public function edit()
    {

    }


    /**
     * Update the specified resource in storage.
     */
    public function update()
    {

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
