<?php

namespace App\Http\Controllers;

use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Http\Resources\ElectionResource;
use App\Models\Company;
use App\Models\Election;
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

    public function index(Request $request, Company $company)
    {
        $elections = ElectionResource::collection($this->electionService->getAll($company))->toArray($request);

        return view('app.company.election.index', compact('company', 'elections'));
    }

    public function create(Company $company)
    {
        return view('app.company.election.create', compact('company'));
    }

    public function store(StoreElectionRequest $request, Company $company): RedirectResponse
    {
        try {
            $company = $this->electionService->create($company, $request->toDto());

            return to_route('elections.index', $company->slug)->with('success', __('messages.election.created'));
        } catch (Throwable $th) {
            return back()->with('error', "خطایی هنگام ایجاد انتخابات رخ داد.");
        }
    }

    public function show(Request $request, Company $company, Election $election): View
    {
        $election = ElectionResource::make($election)->toArray($request);

        return view('app.company.election.show', compact('company', 'election'));
    }

    public function edit(Request $request, Company $company, Election $election): View
    {
        $election = ElectionResource::make($election)->toArray($request);

        return view('app.company.election.edit', compact('company', 'election'));
    }

    public function update(UpdateElectionRequest $request, Company $company, Election $election): RedirectResponse
    {
        try {

            $this->electionService->update($election, $request->toDto());

            return to_route('elections.index', $company->slug)->with('success',  __('messages.election.edited'));
        } catch (Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Company $company, Election $election): RedirectResponse
    {
        try {

            $this->electionService->delete($election);

            return back()->with('success', __('messages.election.deleted'));
        } catch (Throwable $th) {

            return back()->with('error', $th->getMessage());
        }
    }
}
