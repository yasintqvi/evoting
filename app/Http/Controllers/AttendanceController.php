<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Group;
use App\Models\Participant;
use App\Services\AttorneyService;
use Illuminate\Http\Request;
use Log;
use Throwable;

class AttendanceController extends Controller
{
    protected AttorneyService $attendanceService;

    public function __construct(AttorneyService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function create(Group $group, Event $event)
    {
        $hiddenUserIds = $group->managerOnlyUserIds();

        $users = $group->users()->whereNotIn('users.id', $hiddenUserIds)->get();

        $attorneyIds = array_filter($event->participants()->pluck('attorney_id')->toArray());

        $event->load([
            'participants' => fn ($query) => $query
                ->visibleForAttendance($group)
                ->with(['user', 'attorney.user']),
        ]);

        return view('app.group.attendances.create', compact('group', 'event', 'users', 'attorneyIds', 'hiddenUserIds'));
    }

    public function setPresent(Participant $participant)
    {
        try {
            $participant->is_present = !$participant->is_present;
            $participant->save();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.attendances.successfully'),
                200
            ]);
        } catch (\Throwable $e) {
            Log::error('Error toggling participant presence', [
                'participant_id' => $participant->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.attendances.error'),
            ], 500);
        }
    }

    public function getUser(Request $request, Group $group)
    {
        try {
            $search = $request->input('q');
            $page = $request->input('page', 1);
            $perPage = 10;
            $eventId = $request->integer('event_id');
            $currentParticipantId = $request->integer('current_participant_id');

            $hiddenUserIds = $group->managerOnlyUserIds();

            $query = $group->users()
                ->select('users.id', 'users.phone', 'users.first_name', 'users.last_name')
                ->whereNotIn('users.id', $hiddenUserIds);

            // در گروه‌های سهامی خاص فقط سهامداران واقعی نمایش داده شوند.
            if ($group->type === \App\Enums\GroupType::SPECIAL) {
                $query->where(function ($q) {
                    $q->where('group_user.normal_stock_count', '>', 0)
                        ->orWhere('group_user.prefered_stock_count', '>', 0);
                });
            }

            if ($eventId > 0) {
                $query->whereHas('participants', function ($q) use ($eventId, $currentParticipantId) {
                    $q->where('event_id', $eventId)
                        ->where('is_present', true)
                        ->whereNull('attorney_id');

                    if ($currentParticipantId > 0) {
                        $q->where('id', '!=', $currentParticipantId);
                    }
                });
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.phone', 'like', "%{$search}%")
                        ->orWhere('users.first_name', 'like', "%{$search}%")
                        ->orWhere('users.last_name', 'like', "%{$search}%");
                });
            }

            $users = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'results' => $users->items(),
                'pagination' => ['more' => $users->hasMorePages()],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Error fetching users', [
                'group_id' => $group->id,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.attendances.user_error'),
            ], 500);
        }
    }
}
