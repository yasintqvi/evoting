<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use App\Models\Participant;
use App\Models\User;
use App\Services\AttorneyService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttorneyService $attendanceService;

    public function __construct(AttorneyService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Event $event)
    {
        $event = Event::find(1);

        if (! $event) {
            return response()->json([
                'presentCount' => 0,
                'totalCount' => 0,
            ]);
        }

        $totalCount = $event->group->users()->count();
        $presentCount = $event->attendances()->where('status', 1)->count();

        return response()->json([
            'presentCount' => $presentCount,
            'totalCount' => $totalCount,
        ]);
    }

    public function create(Group $group, Event $event)
    {
        $users = $group->users()->with([
            'attendances' => function ($q) use ($event) {
                $q->where('event_id', $event->id);
            },
        ])->get();

        $attorneyIds = array_filter($event->participants()->pluck('attorney_id')->toArray());

        return view('app.group.attendances.create', compact('group', 'event', 'users', 'attorneyIds'));
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

    public function setPresent(Participant $participant)
    {
        try {
            $participant->is_present = ! $participant->is_present;
            $participant->save();

            return response()->json(['status' => 'success',
                'message' => 'با موفقیت ثبت شد.', 200]);
        } catch (\Exception $exception) {
            return response()->json(['status' => 'error',
                'message' => 'با شکست مواجه شد.', 200]);
        }
    }

    public function getUser(Request $request)
    {
        $search = $request->input('q');   // search term
        $page = $request->input('page', 1);
        $perPage = 10;

        $query = User::query()->select('id', 'phone', 'first_name', 'last_name');

        if ($search) {
            $query->where('phone', 'like', "%{$search}%");
        }

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $users->items(),
            'pagination' => ['more' => $users->hasMorePages()],
        ]);
    }
}
