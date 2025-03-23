<?php

namespace App\Http\Controllers;

use App\Enums\ElectionType;
use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Models\Company;
use App\Models\Election;

class ElectionController extends Controller
{
    public function index(Company $company)
    {
        $elections = $company->elections()->latest()->get();

        return view('app.company.election.index', compact('company', 'elections'));
    }

    public function create(Company $company)
    {
        return view('app.company.election.create', compact('company'));
    }

    public function store(StoreElectionRequest $request, Company $company)
    {
        $data = [];

        if ($request->input('type') == ElectionType::PUBLIC_JOINT->value) {
            $data = $request->except('prefered_stock_weight', 'prefered_stock_count', 'normal_stock_count');
        } else {
            $data = $request->validated();
        }

        $company->elections()->create([
            ...$data,
            'user_id' => user()->id
        ]);

        return to_route('elections.index', $company->slug)->with('success', 'انتخابات جدید اضافه شد');
    }

    public function show(Company $company, Election $election)
    {
        return view('app.company.election.show', compact('company', 'election'));
    }

    public function edit(Company $company, Election $election)
    {
        return view('app.company.election.edit', compact('company', 'election'));
    }

    public function update(UpdateElectionRequest $request, Company $company, Election $election)
    {
        $data = [];

        $status = $election->status->value;

        if ($status == 'created') {
            if ($request->input('type') == ElectionType::PUBLIC_JOINT->value) {
                $data = $request->except('prefered_stock_weight', 'prefered_stock_count', 'normal_stock_count');
                $data = array_merge($data, [
                    'prefered_stock_weight' => 0,
                    'prefered_stock_count' => 0,
                    'normal_stock_count' => 0,
                ]);
            } else {
                $data = $request->validated();
            }

            $data['quorum_required'] = $request->has('quorum_required') ? 1 : 0;

            $election->update([
                ...$data,
                'user_id' => user()->id
            ]);

            return to_route('elections.index', $company->slug)->with('success', 'انتخابات ویرایش شد');
        } else {
            return back()->with('error', 'امکان ویرایش وجود ندارد');
        }
    }

    public function destroy(Company $company, Election $election) {}
}
