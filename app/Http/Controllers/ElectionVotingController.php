<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Http\Requests\Election\StoreVotingRequest;
use App\Models\Election;
use App\Models\Company;
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
    public function create(Company $company, Election $election)
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


        return view('app.company.election.voting.create', compact('company', 'election', 'participant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVotingRequest $request, Company $company, Election $election)
    {
        $data = $request->validated();

        if (!isset($data['director_candidates']) || empty($data['director_candidates'])) {
            $data['director_candidates'] = [];
            foreach ($election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->get() as $candidate) {
                $data['director_candidates'][$candidate->id] = 0;
            }
        }

        if (!isset($data['inspector_candidates']) || empty($data['inspector_candidates'])) {
            $data['inspector_candidates'] = [];
            foreach ($election->candidates()->where('candidate_type', CandidateType::INSPECTOR)->get() as $candidate) {
                $data['inspector_candidates'][$candidate->id] = 0;
            }
        }

        if (count(array_filter($data['director_candidates'], fn($item) => $item > 0)) > $election->main_member_count) {
            return back()->withErrors(['director_candidates' => 'تعداد کاندیداهای مدیر بیش از حد مجاز است.']);
        }

        if (count(array_filter($data['inspector_candidates'], fn($item) => $item > 0)) > $election->incpector_main_member_count) {
            return back()->withErrors(['inspector_candidates' => 'تعداد کاندیداهای بازرس بیش از حد مجاز است.']);
        }

        DB::transaction(function () use ($company, $election, $data) {
            $participant = $election->participants()->where('user_id', user()->id)->first();

            $activeRound = $election->rounds()->where('is_active', true)->first();

            if (!$activeRound) {
                $activeRound = $election->rounds()->create(['is_active' => true]);
            }

            foreach ($election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->get() as $candidate) {
                $voteCount = $data['director_candidates'][$candidate->id] ?? 0;

                if ($participant->total_stock < (int) $voteCount) {
                    return back()->withErrors(['director_candidates' => 'تعداد سهام شما کافی نیست.']);
                }

                $participant->votes()->create([
                    "election_round_id" => $activeRound->id,
                    'candidate_id' => $candidate->id,
                    'vote_count' => (int) $voteCount
                ]);
            }

            foreach ($election->candidates()->where('candidate_type', CandidateType::INSPECTOR)->get() as $candidate) {
                $voteCount = $data['inspector_candidates'][$candidate->id] ?? 0;

                if ($participant->total_stock < (int) $voteCount) {
                    return back()->withErrors(['inspector_candidates' => 'تعداد سهام شما کافی نیست.']);
                }

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

        return to_route('elections.index', $company->slug)->with('success', 'رای‌های شما با موفقیت ثبت شدند.');
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

    public function terminate(Request $request, Company $company, Election $election)
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
