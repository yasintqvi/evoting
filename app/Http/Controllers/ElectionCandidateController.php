<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreCandidateRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Services\CandidateService;
use Exception;
use Log;

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
        return view('app.group.election.candidate.index', compact('group', 'election'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Event $event, Election $election)
    {
        $group->load('users');

        return view('app.group.election.candidate.create', compact('group', 'election'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request, Group $group, Election $election)
    {
        try {

            $this->candidateService->create($election, $request->toDto());

            return to_route('elections.index', $group->slug)->with('success', 'کاندید جدید اضافه شد');
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
        $group->load('users');

        return view('app.group.event.election.candidate.edit', compact('group', 'event', 'election'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCandidateRequest $request, Group $group, Election $election)
    {
        try {

            $this->candidateService->update($election, $request->toDto());

            return to_route('elections.index', $group->slug)->with('success', 'کاندیدها با موفقیت به‌روزرسانی شدند.');
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
