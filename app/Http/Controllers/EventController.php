<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\EventRequest;
use App\Models\Event;
use App\Models\Group;
use App\Services\Image\ImageService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Group $group)
    {
        $events = Event::all();

        return view('app.group.event.index', compact('group', 'events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group)
    {
        return view('app.group.event.create', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request, Group $group, ImageService $imageService)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $imageService->setImage($data['logo'])
                ->setExclusiveDirectory('images/events')
                ->save();
        }

        $event = $group->events()->create($data);

        foreach ($group->users as $user) {
            $event->participants()->create([
                'user_id' => $user->id,
                'normal_stock_count' => $user->pivot->normal_stock_count,
                'prefered_stock_count' => $user->pivot->prefered_stock_count,
            ]);
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group, Event $event)
    {
        return view('app.group.event.show', compact('group', 'event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Event $event)
    {
        return view('app.group.event.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Event $event) {}
}
