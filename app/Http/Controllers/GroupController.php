<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\GroupRequest;
use App\Models\Group;
use App\Services\Image\ImageService;
use Log;

class GroupController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index(Group $group)
    {
        $usersCount = $group->users()->count();

        $events = $group->events()
            ->withCount([
                'participants as present_count1' => function ($q) {
                    $q->whereNull('attorney_id')->where('is_present', 1);
                },
                'participants as absent_count1' => function ($q) {
                    $q->whereNull('attorney_id')->where('is_present', 0);
                },
            ])
            ->orderBy('created_at')
            ->get();

        return view('app.group.index', compact('group', 'events', 'usersCount'));
    }

    public function create()
    {
        return view('app.group.create');
    }

    public function store(GroupRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('logo')) {
                $validated['logo'] = $this->imageService
                    ->setImage($request->file('logo'))
                    ->setExclusiveDirectory('images/companies')
                    ->save();
            }

            $group = user()->ownerCompanies()->create($validated);
            $group->users()->attach(user()->id);

            return back()->with('success', __('messages.company.created'));

        } catch (\Throwable $th) {
            Log::error('Error while creating company', [
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.company.create_error'));
        }
    }

    public function edit(Group $group)
    {
        return view('app.group.edit', compact('group'));
    }

    public function update(GroupRequest $request, Group $group)
    {
        Log::info('=== GROUP UPDATE METHOD CALLED ===', [
            'group_id' => $group->id,
            'user_id' => auth()->id(),
            'request_method' => request()->method(),
            'request_url' => request()->url(),
        ]);

        try {
            Log::info('=== GROUP UPDATE REQUEST ===', [
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'all_request_data' => $request->all(),
            ]);

            $validated = $request->validated();

            Log::info('=== VALIDATED DATA ===', [
                'validated_data' => $validated,
            ]);

            // نوع گروه پس از ایجاد قابل تغییر نیست
            unset($validated['type']);

            Log::info('=== DATA AFTER UNSET TYPE ===', [
                'data_after_unset' => $validated,
            ]);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $this->imageService
                    ->setImage($request->file('logo'))
                    ->setExclusiveDirectory('images/companies')
                    ->save();
            }

            Log::info('=== FINAL UPDATE DATA ===', [
                'final_data' => $validated,
            ]);

            $result = $group->update($validated);

            Log::info('=== UPDATE RESULT ===', [
                'update_result' => $result,
                'group_data_after_update' => $group->fresh()->toArray(),
            ]);

            return back()->with('success', __('messages.company.updated'));
        } catch (\Throwable $th) {
            Log::error('Error while updating company', [
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.company.update_error'));
        }
    }

    public function destroy(Group $group)
    {
        $group->delete();

        return to_route('app.index');
    }

    public function leave(Group $group)
    {
        $group->users()->detach(user()->id);

        return to_route('app.index');
    }
}
