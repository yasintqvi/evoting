<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Log;

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
        try {
            $user = User::create($request->validated());

            return redirect()->route('users.index')
                ->with('success', __('messages.user.created'));
        } catch (\Throwable $th) {
            Log::error('Error creating user', [
                'performed_by' => user()->id ?? null,
                'request_data' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.user.create_error'));
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
        try {
            $inputs = $request->validated();
            $user->update($inputs);

            return redirect()->route('users.index')
                ->with('success', __('messages.user.updated'));
        } catch (\Throwable $th) {
            Log::error('Error updating user', [
                'performed_by' => user()->id ?? null,
                'user_id' => $user->id,
                'request_data' => $request->all(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.user.update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
