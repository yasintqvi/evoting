<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\Event;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Event $event)
    {
        $event = Event::find(1);

        if (!$event) {
            return response()->json([
                'presentCount' => 0,
                'totalCount' => 0
            ]);
        }

        $totalCount = $event->group->users()->count();
        $presentCount = $event->attendances()->where('status', 1)->count();

        return response()->json([
            'presentCount' => $presentCount,
            'totalCount' => $totalCount
        ]);
    }

    public function create(Group $group, Event $event)
    {
        $users = $group->users()->with([
            'attendances' => function ($q) use ($event) {
                $q->where('event_id', $event->id);
            }
        ])->get();


        return view('app.group.attendances.create', compact('group', 'event', 'users'));
    }

    public function store(Request $request, Group $group, Event $event)
    {
        $request->validate([
            'attendance.*.status' => 'required|in:0,1',
        ]);


        foreach ($request->attendance as $userid => $data) {
            Attendance::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id' => $userid,
                ],
                [
                    'status' => $data['status'] ?? 0,
                ]
            );
        }

        return back()->with('success', 'حضور و غیاب ثبت شد');
    }

    public function show($groupId, $eventId)
    {
        $group = Group::findOrFail($groupId);
        $event = Event::where('group_id', $groupId)->findOrFail($eventId);

        return view('app.group.attendances.show', compact('group', 'event'));
    }
}
