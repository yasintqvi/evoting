<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\StoreGroupRequest;
use App\Models\Group;
use App\Services\Image\ImageService;

class GroupController extends Controller
{
    public function __construct(protected ImageService $imageService) {}

    public function index(Group $group)
    {
        return view('app.group.index', compact('group'));
    }


    public function create()
    {
        return view('app.group.create');
    }

    public function store(StoreGroupRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->imageService
                ->setImage($request->file('logo'))
                ->setExclusiveDirectory('images/groups')
                ->save();
        }

        $group = user()->ownerGroups()->create($validated);

        $group->users()->attach(user()->id);

        return back()->with('success', __('messages.group_created'));
    }
}
