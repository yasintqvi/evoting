<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ParticipantsImport;
use App\Http\Requests\Election\StoreParticipantRequest;
use App\Http\Requests\Election\StoreParticipaintTableRequest;
use App\Models\Election;
use App\Models\Group;
use App\Models\Participant;
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
        if ($election->status != ElectionStatus::PARTICIPANTS_PENDING) {
            return back();
        }
        
        $participants = $request->validated('participants');

        foreach ($participants as $participant) {
            $election->participants()->create([
                'user_id' => $participant['user_id'],
                'normal_stock_count' => $participant['normal_stock_count'] ?? 0,
                'prefered_stock_count' => $participant['prefered_stock_count'] ?? 0
            ]);
        }

        $election->status = $election->quorum_required ?  ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

        $election->save();

        return to_route('elections.index', $group->slug)->with('success','شرکت کننده جدید اضافه شد');
    }

    public function storeTableParticipent(StoreParticipaintTableRequest $request, Group $group, Election $election)
    {
        if ($election->status != ElectionStatus::PARTICIPANTS_PENDING) {
            return back();
        }
    
        $participants = collect($request->validated('participants'))
        ->filter(function ($participant) {
            return !empty($participant['normal_stock_count']) || !empty($participant['prefered_stock_count']);
        })
        ->map(function ($participant) {
            $participant['normal_stock_count'] = $participant['normal_stock_count'] ?? 0;
            $participant['prefered_stock_count'] = $participant['prefered_stock_count'] ?? 0;
            return $participant;
        });

        foreach ($participants as $participant) {
            $election->participants()->create([
                'user_id' => $participant['user_id'],
                'normal_stock_count' => $participant['normal_stock_count'] ?? 0,
                'prefered_stock_count' => $participant['prefered_stock_count'] ?? 0
            ]);
        }

        $election->status = $election->quorum_required ?  ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

        $election->save();

        return to_route('elections.index', $group->slug)->with('success','شرکت کننده جدید اضافه شد');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, Election $election, Participant $participant)
    {
        if ($election->status === ElectionStatus::PARTICIPANTS_ATTENDEES && !$participant->is_present) {
            $participant->update([
                'is_present' => true
            ]);

            if ((int) (100 * ($election->precentParticipants()->count() / $group->users->count())) > 50) {
                $election->status = ElectionStatus::ONGOING;
                $election->save();

                $election->rounds()->create([
                    'is_active' => true
                ]);
            }
        }

        return back()->with('success' , 'شرکت کننده ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
