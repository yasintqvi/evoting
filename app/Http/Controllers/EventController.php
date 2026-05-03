<?php

namespace App\Http\Controllers;

use App\Http\Requests\Group\EventRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Survey;
use App\Models\Vote;
use App\Services\Image\ImageService;
use Illuminate\Http\Request;
use Log;

class EventController extends Controller
{
    public function index(Group $group)
    {
        // $events = Event::latest()->filter(\request()->all())->get();

        $events = Event::latest()->where('group_id', $group->id)
            ->filter(\request()->all())
            ->get();

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
        try {
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

            return back()->with('success', __('messages.event.created'));

        } catch (\Throwable $th) {
            Log::error('Error while creating event', [
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.event.create_error'));
        }
    }

    public function attendanceStats(Event $event)
    {
        $participants = $event->participants;

        $map = [
            'absent' => 0,
            'present' => 1,
        ];

        return response()->json([
            'present' => $event->participants()->where('is_present', $map['present'])->count(),
            'absent' => $event->participants()->where('is_present', $map['absent'])->count(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group, Event $event)
    {
        $election_ids = $event->elections()->pluck('id')->toArray();
        $surveys_ids = $event->surveys()->pluck('id')->toArray(); 

        $vote_ids = [];

        foreach ($event->elections as $election) {
            $election->votes->each(fn(Vote $vote) => array_push($vote_ids, $vote->id));
        }

        $activities = $group->activities()
            ->orWhere(fn ($q) => $q->where('subject_type', Event::class)->where('subject_id', $event->id))
            ->orWhere(fn ($q) => $q->where('subject_type', Election::class)->whereIn('subject_id', $election_ids))
            ->orWhere(fn ($q) => $q->where('subject_type', Vote::class)->whereIn('subject_id',$vote_ids))
            ->latest()
            ->get();

        return view('app.group.event.show', compact('group', 'event', 'activities'));
    }

    /**
     * Show attendance chart for the event
     */
    public function showAttendance(Group $group, Event $event)
    {
        return view('app.group.attendances.show', compact('group', 'event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Event $event)
    {
        return view('app.group.event.edit', compact('group', 'event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Group $group, Event $event, ImageService $imageService)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                if (!empty($event->logo)) {
                    $imageService->deleteImage($event->logo);
                }

                $data['logo'] = $imageService->setImage($data['logo'])
                    ->setExclusiveDirectory('images/events')
                    ->save();
            }

            $event->update($data);

            return back()->with('success', __('messages.event.updated'));

        } catch (\Throwable $th) {
            Log::error('Error while updating event', [
                'event_id' => $event->id,
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.event.update_error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group, Event $event)
    {

    }
}
