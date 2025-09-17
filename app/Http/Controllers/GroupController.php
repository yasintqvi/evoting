<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\GroupRequest;
use App\Models\Attendance;
use App\Models\Group;
use App\Services\Image\ImageService;

class GroupController extends Controller
{
    public function __construct(protected ImageService $imageService)
    {
    }

    public function index(Group $group)
    {
        $usersCount = $group->users()->count();

        $events = $group->events()->orderBy('created_at')->get();

        return view('app.group.index', compact('group', 'events', 'usersCount'));
    }


    public function create()
    {
        return view('app.group.create');
    }

    public function store(GroupRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService
                ->setImage($request->file('logo'))
                ->setExclusiveDirectory('images/companies')
                ->save();
        }

        $group = user()->ownerCompanies()->create($validated);

        $group->users()->attach(user()->id);

        return back()->with('success', __('messages.company_updated'));
    }

    public function edit(Group $group)
    {
        return view('app.group.edit', compact('group'));
    }

    public function update(GroupRequest $request, Group $group)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService
                ->setImage($request->file('logo'))
                ->setExclusiveDirectory('images/companies')
                ->save();
        }

        $group = $group->update($validated);

        return back()->with('success', __('messages.company_updated'));
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
