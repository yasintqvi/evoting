<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Http\Resources\ElectionResource;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Position;
use App\Models\User;
use App\Services\ElectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ElectionController extends Controller
{
    private ElectionService $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    public function index(Request $request, Group $group, Event $event)
    {
        $elections = ElectionResource::collection($this->electionService->getAll($event))->toArray($request);

        return view('app.group.event.election.index', compact('group', 'elections', 'event'));
    }

    public function create(Group $group, Event $event)
    {
        $users = User::select('id', 'first_name', 'last_name')->get();

        $positions = Position::select('id', 'title')->get();

        return view('app.group.event.election.create', compact('group', 'event', 'users', 'positions'));
    }

    public function store(StoreElectionRequest $request, Group $group, Event $event): RedirectResponse
    {
        try {
            $group = $this->electionService->create($group, $event, $request->toDto());

            return to_route('elections.index', [$group->slug, $event->id])->with('success', __('messages.election.created'));
        } catch (Throwable $th) {
            Log::error('Error creating election', [
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', __('messages.election.error'));
        }
    }

    public function show(Request $request, Group $group, Election $election, Event $event): View
    {
        $election = ElectionResource::make($election)->toArray($request);

        return view('app.group.event.election.show', compact('group', 'election'));
    }

    public function edit(Request $request, Group $group, Event $event, Election $election): View
    {
        $users = User::select('id', 'first_name', 'last_name')->get();

        $positions = Position::all();

        return view('app.group.event.election.edit', compact('group', 'event', 'users', 'election', 'positions'));
    }

    public function update(UpdateElectionRequest $request, Group $group, Event $event, Election $election): RedirectResponse
    {
        try {

            $this->electionService->update($election, $request->toDto());

            return to_route('elections.index', [$group->slug, $event->id])->with('success', __('messages.election.edited'));
        } catch (Throwable $th) {
            Log::error('Error updating election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Group $group, Event $event, Election $election): RedirectResponse
    {
        try {

            $this->electionService->delete($election);

            return back()->with('success', __('messages.election.deleted'));
        } catch (Throwable $th) {
            Log::error('Error deleting election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', $th->getMessage());
        }
    }
}
