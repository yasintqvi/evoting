<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use App\Http\Requests\Election\StoreVotingRequest;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectionVotingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Election $election)
    {
        if ($election->status != ElectionStatus::ONGOING) {
            return back();
        }

        if (!$election->participants()->where('user_id', user()->id)->exists()) {
            return back();
        }

        $participant = $election->participants()->where('user_id', user()->id)->first();

        $activeRound = $election->rounds()->where('is_active', true)->first();

        if ($participant->votes()->where('election_round_id', $activeRound->id)->first()) {
            return back();
        }

        $election->load('candidates');


        return view('app.group.election.voting.create', compact('group', 'election', 'participant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVotingRequest $request, Group $group, Election $election)
    {
        $data = $request->validated();

        $data['director_candidates'] = array_filter($data['director_candidates'], function ($value) {
            return $value != 0;
        });

        $data['inspector_candidates'] = array_filter($data['inspector_candidates'], function ($value) {
            return $value != 0;
        });

        if (count($data['director_candidates']) > $election->main_member_count) {
            return back();
        }

        if (count($data['inspector_candidates']) > $election->incpector_main_member_count) {
            return back();
        }

        DB::transaction(function () use ($group, $election, $data) {

            $participant = $election->participants()->where('user_id', user()->id)->first();

            $activeRound = $election->rounds()->where('is_active', true)->first();

            if (!$activeRound) {
                $activeRound = $election->rounds()->create();
            }

            foreach ($data['director_candidates'] as $directorCandidateId => $voteCount) {

                if ($participant->total_stock < (int) $voteCount) {
                    return back();
                }

                $candidate = $election->candidates()
                    ->where('candidate_type', CandidateType::DIRECTOR)
                    ->where('id', $directorCandidateId)->firstOrFail();

                $participant->votes()->create([
                    "election_round_id" => $activeRound->id,
                    'candidate_id' => $candidate->id,
                    'vote_count' => (int) $voteCount
                ]);
            }

            foreach ($data['inspector_candidates'] as $inspectorCandidateId => $voteCount) {

                if ($participant->total_stock < (int) $voteCount) {
                    return back();
                }

                $candidate = $election->candidates()
                    ->where('candidate_type', CandidateType::INSPECTOR)
                    ->where('id', $inspectorCandidateId)->firstOrFail();

                $participant->votes()->create([
                    "election_round_id" => $activeRound->id,
                    'candidate_id' => $candidate->id,
                    'vote_count' => (int) $voteCount
                ]);
            }

            $participant->update([
                'is_present' => true
            ]);
        });


        return to_route('elections.index', $group->slug);
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

    public function terminate(Request $request, Group $group, Election $election)
    {
        $election->rounds->map(fn($round) => $round->update([
            'is_active' => false,
        ]));

        $election->update([
            'status' => ElectionStatus::COMPLETED
        ]);

        return back();
    }
}
