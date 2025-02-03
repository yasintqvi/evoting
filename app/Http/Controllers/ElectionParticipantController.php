<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use App\Http\Requests\Election\StoreParticipantRequest;
use App\Models\Election;
use App\Models\Group;
use Illuminate\Http\Request;

class ElectionParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Election $election)
    {
        return view('app.group.election.participant.create', compact('group', 'election'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipantRequest $request, Group $group, Election $election)
    {
        $participants = $request->validated('participants');

        foreach ($participants as $participant) {
            $election->participants()->create([
                'user_id' => $participant['user_id'],
                'normal_stock_count' => $participant['normal_stock_count'] ?? 0,
                'prefered_stock_count' => $participant['prefered_stock_count'] ?? 0
            ]);
        }

        $election->status = $election->quorum_required ?  ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::ONGOING;

        $election->save();

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
}
