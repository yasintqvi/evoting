<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Election\StoreCandidateRequest;
use App\Models\Candidate;
use App\Models\Company;
use App\Models\Election;
use Illuminate\Http\Request;

class ElectionCandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Company $company, Election $election)
    {
        return view('app.company.election.candidate.index', compact('company', 'election'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Company $company, Election $election)
    {
        $company->load('users');

        return view('app.company.election.candidate.create', compact('company', 'election'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCandidateRequest $request, Company $company, Election $election)
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

        return to_route('elections.index', $company->slug)->with('success', 'کاندید جدید اضافه شد');
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
    public function edit(Company $company, Election $election)
    {
        $company->load('users');
        return view('app.company.election.candidate.edit', compact('company', 'election',));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCandidateRequest $request, Company $company, Election $election)
    {
        if ($election->status != ElectionStatus::CREATED) {
            return back()->with('error', 'امکان ویرایش در این وضعیت وجود ندارد.');
        }

        $data = $request->validated();

        $election->candidates()
            ->whereNotIn('user_id', array_merge($data['main_candidates_ids'], $data['incpector_candidates_ids']))
            ->delete();

        foreach ($data['main_candidates_ids'] as $mainCandidateId) {
            $election->candidates()->updateOrCreate(
                ['user_id' => $mainCandidateId],
                ['candidate_type' => CandidateType::DIRECTOR]
            );
        }

        foreach ($data['incpector_candidates_ids'] as $incpectorCandidateId) {
            $election->candidates()->updateOrCreate(
                ['user_id' => $incpectorCandidateId],
                ['candidate_type' => CandidateType::INSPECTOR]
            );
        }

        return to_route('elections.index', $company->slug)->with('success', 'کاندیدها با موفقیت به‌روزرسانی شدند.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
