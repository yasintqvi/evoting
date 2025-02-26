<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Election\StoreCandidateRequest;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Group;
use Illuminate\Http\Request;

class ElectionCandidateController extends Controller
{
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
    public function create(Group $group, Election $election)
    {
        $group->load('users');

        return view('app.group.election.candidate.create', compact('group', 'election'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request, Group $group, Election $election)
    {
        if ($election->status != ElectionStatus::CREATED) {
            return back();
        }


        $data = $request->validated();

        $election->candidates()->delete();

        foreach ($data['main_candidates_ids'] as $mainCandidateId) {
            $election->candidates()->create([
                'user_id' => $mainCandidateId,
                'candidate_type' => CandidateType::DIRECTOR,
            ]);
        }

        foreach ($data['incpector_candidates_ids'] as $incpectorCandidateId) {
            $election->candidates()->create([
                'user_id' => $incpectorCandidateId,
                'candidate_type' => CandidateType::INSPECTOR,
            ]);
        }

        $election->status = ElectionStatus::PARTICIPANTS_PENDING;

        $election->save();

        return to_route('elections.index', $group->slug)->with('success','کاندید جدید اضافه شد');
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
    public function edit(Group $group, Election $election, Candidate $candidate)
    {
        //
    }

    public function editCandidate(Group $group, Election $election)
    {
        $group->load('users');
        return view('app.group.election.candidate.edit', compact('group', 'election',));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, Election $election)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
