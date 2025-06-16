<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Http\Resources\ElectionResource;
use App\Models\Group;
use App\Models\Election;
use App\Models\User;
use App\Services\ElectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class ElectionController extends Controller
{

    private ElectionService $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    public function index(Request $request, Group $group)
    {
        $elections = ElectionResource::collection($this->electionService->getAll($group))->toArray($request);

        return view('app.group.election.index', compact('group', 'elections'));
    }

    public function create(Group $group)
    {
        $users = User::select("id", "first_name", "last_name")->get();

        return view('app.group.election.create', compact('group', 'users'));
    }

    public function store(StoreElectionRequest $request, Group $group): RedirectResponse
    {
        try {
            $group = $this->electionService->create($group, $request->toDto());

            return to_route('elections.index', $group->slug)->with('success', __('messages.election.created'));
        } catch (Throwable $th) {
            return back()->with('error', "خطایی هنگام ایجاد انتخابات رخ داد.");
        }
    }

    public function show(Request $request, Group $group, Election $election): View
    {
        $election = ElectionResource::make($election)->toArray($request);

        return view('app.group.election.show', compact('group', 'election'));
    }

    public function edit(Request $request, Group $group, Election $election): View
    {
        $users = User::select("id", "first_name", "last_name")->get();

        $election = ElectionResource::make($election)->toArray($request);

        return view('app.group.election.edit', compact('group', 'users', 'election'));
    }

    public function update(UpdateElectionRequest $request, Group $group, Election $election): RedirectResponse
    {
        try {

            $this->electionService->update($election, $request->toDto());

            return to_route('elections.index', $group->slug)->with('success',  __('messages.election.edited'));
        } catch (Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Group $group, Election $election): RedirectResponse
    {
        try {

            $this->electionService->delete($election);

            return back()->with('success', __('messages.election.deleted'));
        } catch (Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
}
