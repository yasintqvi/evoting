<?php

namespace App\Http\Controllers;

use App\Enums\GroupType;
use App\Http\Requests\User\StoreGroupUserRequest;
use App\Http\Requests\User\UpdateGroupUserRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group)
    {

        $search = request('search');
        $filters = request()->all(); // get request filters

        $attorneyRepresentativeUserIds = DB::table('participants as att_p')
            ->join('events', 'events.id', '=', 'att_p.event_id')
            ->join('group_user', function ($join) {
                $join->on('group_user.user_id', '=', 'att_p.user_id')
                    ->on('group_user.group_id', '=', 'events.group_id');
            })
            ->where('events.group_id', $group->id)
            ->whereNull('att_p.deleted_at')
            ->where('group_user.normal_stock_count', 0)
            ->where('group_user.prefered_stock_count', 0)
            ->whereExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('participants as pr')
                    ->whereColumn('pr.attorney_id', 'att_p.id')
                    ->whereColumn('pr.event_id', 'att_p.event_id')
                    ->whereNull('pr.deleted_at');
            })
            ->distinct()
            ->pluck('att_p.user_id')
            ->all();

        $attorneyOnlyUserIds = DB::table('participants as att_p')
            ->join('events', 'events.id', '=', 'att_p.event_id')
            ->join('group_user', function ($join) {
                $join->on('group_user.user_id', '=', 'att_p.user_id')
                    ->on('group_user.group_id', '=', 'events.group_id');
            })
            ->where('events.group_id', $group->id)
            ->whereNull('att_p.deleted_at')
            ->where('att_p.normal_stock_count', 0)
            ->where('att_p.prefered_stock_count', 0)
            ->where('att_p.delegated_normal_stock_count', 0)
            ->where('att_p.delegated_prefered_stock_count', 0)
            ->where('group_user.normal_stock_count', 0)
            ->where('group_user.prefered_stock_count', 0)
            ->whereNotExists(function ($q) {
                $q->select(DB::raw('1'))
                    ->from('participants as pr')
                    ->whereColumn('pr.attorney_id', 'att_p.id')
                    ->whereNull('pr.deleted_at');
            })
            ->distinct()
            ->pluck('att_p.user_id')
            ->all();

        $hiddenAttorneyUserIds = array_values(array_unique(array_merge($attorneyRepresentativeUserIds, $attorneyOnlyUserIds)));

        $group->load([
            'users' => function ($query) use ($search, $filters, $hiddenAttorneyUserIds) {
                if ($hiddenAttorneyUserIds !== []) {
                    $query->whereNotIn('users.id', $hiddenAttorneyUserIds);
                }
                if ($search) {
                    $query->where(
                        fn($q) => $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    );
                }
                if (isset($filters['status'])) {
                    if ($filters['status'] == 1) {
                        $query->where('is_active', 1);
                    }
                    if ($filters['status'] == 2) {
                        $query->where('is_active', 0);
                    }
                }
                $query->latest();
            },
        ]);

        $users = $group->users;
        $stockUsersQuery = $group->users();

        if ($hiddenAttorneyUserIds !== []) {
            $stockUsersQuery->whereNotIn('users.id', $hiddenAttorneyUserIds);
        }

        $allocatedNormalStock = (clone $stockUsersQuery)->sum('group_user.normal_stock_count');
        $allocatedPreferedStock = (clone $stockUsersQuery)->sum('group_user.prefered_stock_count');

        return view('app.group.users.index', compact('group', 'users', 'allocatedNormalStock', 'allocatedPreferedStock'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group)
    {
        $current_group_users = $group->users->pluck('id')->toArray();
        $users = User::whereNotIn('id', $current_group_users)->get();

        return view('app.group.users.create', compact('group', 'users'));
    }

    public function addUsers(Request $request, Group $group)
    {
        $request->validate([
            'users.*' => ['exists:users,id'],
        ]);

        $group->users()->attach($request->input('users'));

        return back()->with('success', 'کاربران جدید به گروه اضافه شدند.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupUserRequest $request, Group $group)
    {
        try {
            $validated = $request->validated();

            $user = User::where('phone', $validated['phone'])->first();

            if ($user) {

                if ($group->users()->where('users.id', $user->id)->exists()) {
                    return back()->with('error', 'این کاربر قبلاً در این گروه عضو شده است.');
                }

                if ($user->nationalcode !== $validated['nationalcode']) {
                    return back()->with('error', 'کاربری با این شماره تلفن وجود دارد اما کد ملی مطابقت ندارد.');
                }

            } else {

                $user = User::create($validated);
            }

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

        $allocatedNormal = $group->users()->sum('group_user.normal_stock_count');
        $allocatedPrefered = $group->users()->sum('group_user.prefered_stock_count');

        // هنگام ویرایش، سهم کاربر فعلی را از محاسبه کم نکنیم چون ممکن است تغییر کند
        // اما برای نمایش "باقی‌مانده"، منطقی است که فرض کنیم سهم کاربر فعلی هم جزو تخصیص‌یافته‌هاست
        // یا می‌توانیم سهم کاربر فعلی را کم کنیم تا نشان دهیم چقدر "آزاد" است اگر سهم او را صفر کنیم
        // معمولاً باقی‌مانده واقعی (کل منهای همه تخصیص‌یافته‌ها) نمایش داده می‌شود

        $remainingNormal = $group->normal_stock_count - $allocatedNormal;
        $remainingPrefered = $group->prefered_stock_count - $allocatedPrefered;

        // اگر بخواهیم سهم کاربر فعلی را هم به عنوان "قابل استفاده مجدد" در نظر بگیریم:
        // $remainingNormal += $user->pivot->normal_stock_count;
        // $remainingPrefered += $user->pivot->prefered_stock_count;
        // اما معمولاً باقی‌مانده واقعی مفیدتر است. کاربر می‌تواند ببیند چقدر جای خالی دارد.

        return view('app.group.users.edit', compact('group', 'user', 'remainingNormal', 'remainingPrefered'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupUserRequest $request, Group $group, User $user)
    {
        try {
            $validated = $request->validated();

            // بررسی کنیم آیا کاربری با این شماره تلفن وجود دارد (که خود این کاربر نباشد)
            $existingUser = User::where('phone', $validated['phone'])
                ->where('id', '!=', $user->id)
                ->first();

            if ($existingUser) {
                return back()->with('error', 'این شماره تلفن قبلاً برای کاربر دیگری ثبت شده است.');
            }

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
                        fn($q) => $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$search%"])
                    );
                }

                $query->latest();
            },
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
