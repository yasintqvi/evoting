<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\ElectionRound;
use App\Models\Group;
use Illuminate\Http\Request;

class ElectionRoundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Group $group, Election $election) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Group $group, Election $election)
    {
        if ($election->status != ElectionStatus::WAITING_TO_START) {
            return back();
        }

        if ($election->rounds()->where('is_active', true)->exists()) {
            return back();
        }

        $election->rounds()->create([
            'is_active' => true
        ]);

        $election->update([
            'status' => ElectionStatus::ONGOING
        ]);

        return to_route('elections.index', $group->slug);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group, Election $election, ElectionRound $electionRound)
    {
        $electionRound = $election->rounds()->where('id', $electionRound->id)->first();
        $candidates = $election->candidates;

        $directorCandidatesQuery = $candidates->where('candidate_type', CandidateType::DIRECTOR->value);
        $inspectorCandidatesQuery = $candidates->where('candidate_type', CandidateType::INSPECTOR->value);

        $votes = $electionRound?->votes;

        $directorCandidates = [];
        $inspectorCandidates = [];

        if ($votes) {
            $directorCandidates = $directorCandidatesQuery->map(function ($candidate) use ($votes) {
                return [
                    'name' => $candidate->user->full_name,
                    'votes' => $votes->where('candidate_id', $candidate->id)->sum('vote_count')
                ];
            })->sortByDesc('votes')->toArray();

            $inspectorCandidates = $inspectorCandidatesQuery->map(function ($candidate) use ($votes) {
                return [
                    'name' => $candidate->user->full_name,
                    'votes' => $votes->where('candidate_id', $candidate->id)->sum('vote_count')
                ];
            })->sortByDesc('votes')->toArray();
        }

        return view('app.group.election.round.show', compact(
            'company',
            'election',
            'electionRound',
            'directorCandidates',
            'inspectorCandidates'
        ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
