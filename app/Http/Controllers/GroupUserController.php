<?php

namespace App\Http\Controllers;

use App\Enums\GroupType;
use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        return view('app.group.users.create', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(StoreGroupUserRequest $request, Group $group)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        $group->users()->attach($user->id, [
            'normal_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['normal_stock_count'] ?? 0) : 0,
            'prefered_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['prefered_stock_count'] ?? 0) : 0,
        ]);

        return back()->with('success', 'کاربر برای این گروه ایجاد شد');
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
    public function edit(Group $group, User $user)
    {
        $user = $group->users()
            ->where('users.id', $user->id)
            ->withPivot('normal_stock_count', 'prefered_stock_count')
            ->firstOrFail();

        return view('app.group.users.edit', compact('group', 'user'));
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateGroupUserRequest $request, Group $group, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);

        $group->users()->updateExistingPivot($user->id, [
            'normal_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['normal_stock_count'] ?? 0) : 0,
            'prefered_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['prefered_stock_count'] ?? 0) : 0,
        ]);

        return back()->with('success', 'کاربر و اطلاعات سهام در این گروه بروزرسانی شد');
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
