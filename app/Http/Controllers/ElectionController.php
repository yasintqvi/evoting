<?php

namespace App\Http\Controllers;

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

    public function store(StoreElectionRequest $request) {}

    public function details(Group $group, Election $election)
    {
        return view('app.group.election.details', compact('group', 'election'));
    }

    public function edit(Group $group, Election $election) {}

    public function update(UpdateElectionRequest $request, Group $group, Election $election) {}

    public function destroy(Group $group, Election $election) {}
}
