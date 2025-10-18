<?php

namespace App\Http\Controllers;

use App\Enums\GroupType;
use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Log;

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
                        fn($q) => $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    );
                }

                $query->latest();
            },
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
        try {
            $validated = $request->validated();

            $user = User::create($validated);

            $group->users()->attach($user->id, [
                'normal_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['normal_stock_count'] ?? 0) : 0,
                'prefered_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['prefered_stock_count'] ?? 0) : 0,
            ]);

            return back()->with('success', __('messages.group_user.created'));

        } catch (\Throwable $th) {
            Log::error('Error while creating user for group', [
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.group_user.create_error'));
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
        try {
            $validated = $request->validated();

            $user->update($validated);

            $group->users()->updateExistingPivot($user->id, [
                'normal_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['normal_stock_count'] ?? 0) : 0,
                'prefered_stock_count' => $group->type === GroupType::SPECIAL ? ($validated['prefered_stock_count'] ?? 0) : 0,
            ]);

            Log::info('Group user updated successfully', [
                'group_id' => $group->id,
                'user_id' => $user->id,
                'performed_by' => auth()->id(),
            ]);

            return back()->with('success', __('messages.group_user.updated_successfully'));

        } catch (\Throwable $th) {
            Log::error('Error while updating group user', [
                'group_id' => $group->id,
                'user_id' => $user->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.group_user.update_failed'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, User $user)
    {
        $group->users()->detach($user);

        return back()->with('success', __('messages.company_user_delete'));
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
        try {
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
                    ],
                ]);
            }

            Log::info('Participants added to group successfully', [
                'group_id' => $group->id,
                'user_ids' => array_keys($validated['users']),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('success', __('messages.group_user.participants_added_successfully'));

        } catch (\Throwable $th) {
            Log::error('Error while adding participants to group', [
                'group_id' => $group->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.group_user.participants_add_failed'));
        }
    }
}


