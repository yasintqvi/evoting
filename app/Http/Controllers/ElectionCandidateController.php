<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Election\StoreCandidateRequest;
use App\Models\Company;
use App\Models\Election;
use App\Services\CandidateService;
use Exception;
use Throwable;

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
        try {

            $this->candidateService->create($election, $request->toDto());

            return to_route('elections.index', $company->slug)->with('success', 'کاندید جدید اضافه شد');
        } catch (Exception $e) {
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
        try {

            $this->candidateService->update($election, $request->toDto());

            return to_route('elections.index', $company->slug)->with('success', 'کاندیدها با موفقیت به‌روزرسانی شدند.');

        } catch (Exception $e) {

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
