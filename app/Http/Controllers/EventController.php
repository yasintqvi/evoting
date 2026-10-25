<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use App\Http\Requests\Group\EventRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Vote;
use App\Services\Image\ImageService;
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
                    'group_id' => $group->id,
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
        $map = [
            'absent' => 0,
            'present' => 1,
        ];

        $statsQuery = $event->participants()
            ->visibleForAttendance($event->group)
            ->whereNotIn('id', $event->attorneyOnlyParticipantIds());

        $attendees = (clone $statsQuery)
            ->with('user')
            ->orderByDesc('is_present')
            ->orderBy('present_at')
            ->get()
            ->map(fn ($participant) => [
                'name' => $participant->user->full_name,
                'is_present' => (bool) $participant->is_present,
                'present_at' => optional($participant->present_at)->timestamp,
            ]);

        return response()->json([
            'present' => (clone $statsQuery)->where('is_present', $map['present'])->count(),
            'absent' => (clone $statsQuery)->where('is_present', $map['absent'])->count(),
            'attendees' => $attendees,
            'quorum' => $event->attendanceQuorum(),
        ]);
    }

    /**
     * آمار زنده انتخابات رویداد برای نمایش روی پروژکتور.
     */
    public function liveElectionStats(Event $event)
    {
        $elections = $event->elections()
            ->whereIn('status', [
                ElectionStatus::ONGOING->value,
                ElectionStatus::WAITING_TO_START->value,
                ElectionStatus::PARTICIPANTS_ATTENDEES->value,
                ElectionStatus::CREATED->value,
            ])
            ->latest('id')
            ->get();

        $presentVoters = (int) $event->participants()
            ->visibleForAttendance($event->group)
            ->whereNotIn('id', $event->attorneyOnlyParticipantIds())
            ->where('is_present', true)
            ->whereNull('attorney_id')
            ->count();

        $ongoing = [];
        $waiting = [];

        foreach ($elections as $election) {
            $votedCount = (int) Vote::query()
                ->where('election_id', $election->id)
                ->distinct('participant_id')
                ->count('participant_id');

            $eligible = max(1, $presentVoters);
            $percent = min(100, (int) round(($votedCount / $eligible) * 100));

            $item = [
                'id' => $election->id,
                'title' => $election->title,
                'status' => $election->status->value,
                'status_label' => $election->status->toFa(),
                'voted_count' => $votedCount,
                'eligible_count' => $presentVoters,
                'percent' => $percent,
                'starts_at' => $election->starts_at
                    ? verta($election->starts_at->copy()->setTimezone('Asia/Tehran'))->format('Y/m/d H:i')
                    : null,
            ];

            if ($election->status === ElectionStatus::ONGOING) {
                $ongoing[] = $item;
            } else {
                $waiting[] = $item;
            }
        }

        return response()->json([
            'ongoing' => $ongoing,
            'waiting' => $waiting,
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
            $election->votes->each(fn (Vote $vote) => array_push($vote_ids, $vote->id));
        }

        $activities = $group->activities()
            ->orWhere(fn ($q) => $q->where('subject_type', Event::class)->where('subject_id', $event->id))
            ->orWhere(fn ($q) => $q->where('subject_type', Election::class)->whereIn('subject_id', $election_ids))
            ->orWhere(fn ($q) => $q->where('subject_type', Vote::class)->whereIn('subject_id', $vote_ids))
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
                if (! empty($event->logo)) {
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
        try {
            // اگر انتخاباتی از این رویداد شروع شده باشد، حذف رویداد مجاز نیست.
            $hasStartedElection = $event->elections()
                ->whereIn('status', [
                    ElectionStatus::ONGOING->value,
                    ElectionStatus::COMPLETED->value,
                    ElectionStatus::CANCELED->value,
                ])
                ->exists();

            if ($hasStartedElection) {
                return back()->with('error', __('messages.event.has_started_election'));
            }

            $event->delete();

            return redirect()
                ->route('events.index', $group)
                ->with('success', __('messages.event.deleted'));
        } catch (\Throwable $th) {
            Log::error('Error while deleting event', [
                'event_id' => $event->id,
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.event.delete_error'));
        }
    }
}
