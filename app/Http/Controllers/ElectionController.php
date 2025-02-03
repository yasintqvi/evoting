<?php

namespace App\Http\Controllers;

use App\Enums\ElectionType;
use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Models\Election;
use App\Models\Group;

class ElectionController extends Controller
{
    public function index(Group $group)
    {
        $elections = $group->elections()->latest()->get();

        return view('app.group.election.index', compact('group', 'elections'));
    }

    public function create(Group $group)
    {
        return view('app.group.election.create', compact('group'));
    }

    public function store(StoreElectionRequest $request, Group $group)
    {
        $data = [];

        if ($request->input('type') == ElectionType::PUBLIC_JOINT->value) {
            $data = $request->except('prefered_stock_weight', 'prefered_stock_count', 'normal_stock_count');
        } else {
            $data = $request->validated();
        }

        $group->elections()->create([
            ...$data,
            'user_id' => user()->id
        ]);

        return to_route('elections.index', $group->slug);
    }

    public function show(Group $group, Election $election)
    {
        return view('app.group.election.show', compact('group', 'election'));
    }

    public function edit(Group $group, Election $election) {}

    public function update(UpdateElectionRequest $request, Group $group, Election $election) {}

    public function destroy(Group $group, Election $election) {}
}
