<?php

namespace App\Http\Controllers;

use App\Events\AttendanceUpdated;
use App\Http\Requests\Election\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\Election;
use App\Models\Event;
use App\Services\AttendanceService;
use Exception;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function create(Group $group, Event $event)
    {
        $users = $group->users;

        return view('app.group.attendances.create', compact('group', 'event', 'users'));
    }

    public function store(Request $request, Group $group, Event $event)
    {
        // try {
        //     $this->attendanceService->create($request->toDto(), $event);

        //     return to_route('elections.index', [$group->slug]);
        // } catch (Exception $e) {
        //     return back()->with('error', $e->getMessage());
        // }

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
}
