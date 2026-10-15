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
            $participant->present_at = $participant->is_present ? now() : null;
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

            // کاربرانی که در این رویداد وکیلِ کسی هستند (برای نمایش در لیست و برچسب "وکیل").
            // attorney_id روی participants به ردیف وکیل اشاره می‌کند؛ ابتدا به user_id نگاشت می‌شود.
            $attorneyUserIds = collect();
            if ($eventId > 0) {
                $attorneyParticipantIds = Participant::query()
                    ->where('event_id', $eventId)
                    ->whereNotNull('attorney_id')
                    ->pluck('attorney_id')
                    ->unique()
                    ->values();

                if ($attorneyParticipantIds->isNotEmpty()) {
                    $attorneyUserIds = Participant::query()
                        ->whereIn('id', $attorneyParticipantIds)
                        ->pluck('user_id')
                        ->unique()
                        ->values();
                }
            }

            $hiddenUserIds = $group->managerOnlyUserIds();

            $query = $group->users()
                ->select('users.id', 'users.phone', 'users.first_name', 'users.last_name')
                ->whereNotIn('users.id', $hiddenUserIds);

            // در گروه‌های سهامی خاص فقط سهامداران واقعی یا وکیل‌های فعال همین رویداد نمایش داده شوند؛
            // وکیل بیرونی روی pivot گروه سهام صفر دارد و بدون این شرط از لیست حذف می‌شد.
            if ($group->type === \App\Enums\GroupType::SPECIAL) {
                $query->where(function ($q) use ($attorneyUserIds) {
                    $q->where('group_user.normal_stock_count', '>', 0)
                        ->orWhere('group_user.prefered_stock_count', '>', 0);

                    if ($attorneyUserIds->isNotEmpty()) {
                        $q->orWhereIn('users.id', $attorneyUserIds->all());
                    }
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

            $results = collect($users->items())->each(function ($user) use ($attorneyUserIds) {
                $user->is_attorney = $attorneyUserIds->contains($user->id);

                return $user;
            });

            return response()->json([
                'results' => $results,
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
