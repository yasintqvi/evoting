<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreAttendanceRequest;
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
        // $election->load('participants.user');

        return view('app.group.attendances.create', compact('group', 'event'));
    }

    public function store(StoreAttendanceRequest $request, Group $group, Event $event)
    {
        try {
            $this->attendanceService->create($request->toDto(), $event);

            return back()->with('success', 'اطلاعات با موفقیت بروز رسانی شد.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
