<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use App\Http\Requests\Election\StoreParticipaintTableRequest;
use App\Http\Requests\Election\StoreParticipantRequest;
use App\Models\Election;
use App\Models\Group;
use App\Models\Participant;
use DB;
use Illuminate\Http\Request;
use Log;

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
        try {
            if ($election->status != ElectionStatus::PARTICIPANTS_ATTENDEES) {
                return back();
            }

            $participants = $request->validated('participants');

            foreach ($participants as $participant) {
                $election->participants()->create([
                    'user_id' => $participant['user_id'],
                    'normal_stock_count' => $participant['normal_stock_count'] ?? 0,
                    'prefered_stock_count' => $participant['prefered_stock_count'] ?? 0,
                ]);
            }

            $election->status = $election->quorum_required ? ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

            $election->save();

            return to_route('elections.index', $group->slug)->with('success', __('messages.participant.success'));

        } catch (\Throwable $th) {
            Log::error('Error while adding participants to election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.participant.error'));
        }
    }

    public function storeTableParticipent(StoreParticipaintTableRequest $request, Group $group, Election $election)
    {
        try {
            if ($election->status != ElectionStatus::PARTICIPANTS_ATTENDEES) {
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
                    'prefered_stock_count' => $participant['prefered_stock_count'] ?? 0,
                ]);
            }

            $election->status = $election->quorum_required ? ElectionStatus::PARTICIPANTS_ATTENDEES : ElectionStatus::WAITING_TO_START;

            $election->save();

            return to_route('elections.index', $group->slug)->with('success', __('messages.participant.created'));
        } catch (\Throwable $th) {
            Log::error('Error while adding table participants to election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.participant.error'));
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group, Election $election, Participant $participant)
    {
        try {
            if ($election->status === ElectionStatus::PARTICIPANTS_ATTENDEES && !$participant->is_present) {
                $participant->update([
                    'is_present' => true,
                ]);

                if ($election->precentParticipants() > 50) {
                    $election->status = ElectionStatus::ONGOING;
                    $election->save();

                    $election->rounds()->create([
                        'is_active' => true,
                    ]);
                }
            }

            return back()->with('success', __('messages.participant.updated'));
        } catch (\Throwable $th) {
            Log::error('Error updating participant', [
                'participant_id' => $participant->id,
                'election_id' => $election->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', __('messages.participant.error_update'));
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
