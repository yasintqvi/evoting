<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreCandidateRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use App\Services\CandidateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ElectionCandidateController extends Controller
{
    protected CandidateService $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Group $group, Election $election)
    {
        return view('app.group.event.election.candidate.index', compact('group', 'election'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Event $event, Election $election)
    {
        $group->load(['users' => function ($query) {
            $query->where(function ($q) {
                $q->where('group_user.normal_stock_count', '>', 0)
                    ->orWhere('group_user.prefered_stock_count', '>', 0);
            });
        }]);
        $election->load('position');

        return view('app.group.event.election.candidate.create', compact('group', 'event', 'election'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request, Group $group, Event $event, Election $election)
    {
        try {

            $this->candidateService->create($election, $request->toDto());

            return to_route('elections.index', [$group, $event])->with('success', 'کاندید جدید اضافه شد');
        } catch (Exception $e) {
            Log::error('Error creating candidate', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * جستجوی سهام‌داران همین گروه (برای select2 فیلد انتخاب نامزد بازرس)؛ فقط کسانی که سهام عادی یا ممتاز دارند.
     */
    public function searchGroupShareholders(Request $request, Group $group, Event $event, Election $election)
    {
        $search = $request->input('q');
        $page = (int) $request->input('page', 1);
        $perPage = 10;

        $query = $group->users()
            ->select('users.id', 'users.phone', 'users.first_name', 'users.last_name')
            ->where(function ($q) {
                $q->where('group_user.normal_stock_count', '>', 0)
                    ->orWhere('group_user.prefered_stock_count', '>', 0);
            });

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
    }

    /**
     * ساخت سریع کاربر جدید برای افزودن به‌عنوان نامزد بازرس، بدون نیاز به عضویت در گروه.
     */
    public function quickCreateUser(Request $request, Group $group, Event $event, Election $election)
    {
        $election->load('position');

        if ($election->position?->title !== 'بازرس') {
            return response()->json([
                'status' => 'error',
                'message' => 'افزودن کاربر جدید فقط برای انتخابات بازرس مجاز است.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'digits:11'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::firstOrCreate(
            ['phone' => $validated['phone']],
            [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'password' => bcrypt(substr($validated['phone'], -4)),
            ]
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'phone' => $user->phone,
            ],
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group, Event $event, Election $election)
    {
        if ($election->status->isImmutableStatuses()) {
            return redirect()
                ->route('elections.index', [$group, $event])
                ->with('error', 'پس از شروع انتخابات امکان ویرایش نامزدها وجود ندارد.');
        }

        $group->load(['users' => function ($query) {
            $query->where(function ($q) {
                $q->where('group_user.normal_stock_count', '>', 0)
                    ->orWhere('group_user.prefered_stock_count', '>', 0);
            });
        }]);
        $election->load(['position', 'candidates.user']);

        return view('app.group.event.election.candidate.edit', compact('group', 'event', 'election'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCandidateRequest $request, Group $group, Event $event, Election $election)
    {
        try {

            $this->candidateService->update($election, $request->toDto());

            return to_route('elections.index', [
                'group' => $group->slug,
                'event' => $event->slug,
            ])->with('success', 'کاندیدها با موفقیت به‌روزرسانی شدند.');
        } catch (Exception $e) {
            Log::error('Error updating candidates', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
