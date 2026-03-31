<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Enums\Role;
use App\Http\Requests\Election\StoreVotingRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Participant;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionVotingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $participants = \App\Models\Participant::where('user_id', $user->id)
            ->where('is_present', true)
            ->with([
                'event.group',
                'event.elections' => function ($query) {
                    $query->where('status', \App\Enums\ElectionStatus::ONGOING)
                        ->with('candidates.user', 'position');
                }
            ])
            ->get();

        $availableElections = collect();

        foreach ($participants as $participant) {
            $event = $participant->event;
            if ($event && $event->elections) {
                foreach ($event->elections as $election) {
                    $hasVoted = \App\Models\Vote::where('election_id', $election->id)
                        ->where('participant_id', $participant->id)
                        ->exists();

                    if (!$hasVoted && ($election->candidates->count() > 0 || $election->type === \App\Enums\ElectionType::SURVEY)) {
                        $availableElections->push([
                            'election' => $election,
                            'event' => $event,
                            'group' => $event->group,
                            'participant' => $participant,
                            'has_voted' => false,
                        ]);
                    }
                }
            }
        }

        return view('app.elections.my-elections', ['availableElections' => $availableElections, 'title' => 'انتخابات من']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Group $group, Event $event, Election $election)
    {
        if ($election->status != ElectionStatus::ONGOING) {
            return back();
        }

        if ($election->isExpired()) {
            return back()->with('error', 'زمان این انتخابات به پایان رسیده است و امکان رای دادن وجود ندارد.');
        }

        $user = user();
        $isAdmin = $user && $user->hasRole(Role::Manager->value);

        $participant = $event->participants()
            ->with('group')
            ->where('user_id', $user->id)
            ->first();

        if (!$participant && $isAdmin) {
            $participant = Participant::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'group_id' => $group->id,
                'is_present' => true,
                'normal_stock_count' => $election->normal_stock_count ?? 0,
                'prefered_stock_count' => $election->prefered_stock_count ?? 0,
            ]);
        }

        if (!$participant) {
            return back()->with('error', 'شما در این رویداد شرکت کننده نیستید.');
        }

        // جلوگیری از شرکت کاربرانی که مسدود شده‌اند
        if ($election->blockedUsers()->wherePivot('user_id', $user->id)->exists()) {
            return back()->with('error', 'شما مجاز به شرکت در رأی‌گیری این انتخابات نیستید.');
        }

        $hasVoted = Vote::where('election_id', $election->id)
            ->where('participant_id', $participant->id)
            ->exists();

        if ($hasVoted) {
            return back()->with('error', 'شما قبلاً در این انتخابات رای داده‌اید و نمی‌توانید دوباره رای بدهید.');
        }

        $election->load('candidates');

        return view('app.group.event.election.voting.create', compact('group', 'event', 'election', 'participant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreVotingRequest $request, Group $group, Event $event, Election $election)
    // {
    //     try {
    //         $data = $request->validated();

    //         if (!isset($data['director_candidates']) || empty($data['director_candidates'])) {
    //             $data['director_candidates'] = [];
    //             foreach ($election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->get() as $candidate) {
    //                 $data['director_candidates'][$candidate->id] = 0;
    //             }
    //         }

    //         if (!isset($data['inspector_candidates']) || empty($data['inspector_candidates'])) {
    //             $data['inspector_candidates'] = [];
    //             foreach ($election->candidates()->where('candidate_type', CandidateType::INSPECTOR)->get() as $candidate) {
    //                 $data['inspector_candidates'][$candidate->id] = 0;
    //             }
    //         }

    //         if (count(array_filter($data['director_candidates'], fn($item) => $item > 0)) > $election->main_member_count) {
    //             return back()->withErrors(['director_candidates' => __('messages.voting.director_limit')]);
    //         }

    //         if (count(array_filter($data['inspector_candidates'], fn($item) => $item > 0)) > $election->incpector_main_member_count) {
    //             return back()->withErrors(['inspector_candidates' => __('messages.voting.inspector_limit')]);
    //         }

    //         DB::transaction(function () use ($election, $data) {
    //             $participant = $election->participants()->where('user_id', user()->id)->first();

    //             $activeRound = $election->rounds()->where('is_active', true)->first();

    //             if (!$activeRound) {
    //                 $activeRound = $election->rounds()->create(['is_active' => true]);
    //             }

    //             foreach ($election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->get() as $candidate) {
    //                 $voteCount = $data['director_candidates'][$candidate->id] ?? 0;

    //                 if ($participant->total_stock < (int) $voteCount) {
    //                     throw new \Exception(__('messages.voting.insufficient_stock'));
    //                 }

    //                 $participant->votes()->create([
    //                     'election_round_id' => $activeRound->id,
    //                     'candidate_id' => $candidate->id,
    //                     'vote_count' => (int) $voteCount,
    //                 ]);
    //             }

    //             foreach ($election->candidates()->where('candidate_type', CandidateType::INSPECTOR)->get() as $candidate) {
    //                 $voteCount = $data['inspector_candidates'][$candidate->id] ?? 0;

    //                 if ($participant->total_stock < (int) $voteCount) {
    //                     throw new \Exception(__('messages.voting.insufficient_stock'));
    //                 }

    //                 $participant->votes()->create([
    //                     'election_round_id' => $activeRound->id,
    //                     'candidate_id' => $candidate->id,
    //                     'vote_count' => (int) $voteCount,
    //                 ]);
    //             }

    //             $participant->update([
    //                 'is_present' => true,
    //             ]);
    //         });

    //         return to_route('elections.index', $group->slug)->with('success', __('messages.voting.success'));

    //     } catch (\Throwable $th) {
    //         Log::error('Error while storing votes', [
    //             'election_id' => $election->id,
    //             'group_id' => $group->id,
    //             'user_id' => auth()->id(),
    //             'error' => $th->getMessage(),
    //             'trace' => $th->getTraceAsString(),
    //         ]);

    //         return back()->with('error', __('messages.voting.error'));
    //     }
    // }

    public function store(Request $request, Group $group, Event $event, Election $election)
    {
        \Log::info('=== VOTING STORE METHOD CALLED ===', [
            'request_data' => $request->all(),
            'director_candidates' => $request->input('director_candidates', []),
            'election_id' => $election->id,
            'user_id' => auth()->id(),
        ]);

        try {
            if ($election->status !== ElectionStatus::ONGOING) {
                if (in_array($election->status, [ElectionStatus::COMPLETED, ElectionStatus::CANCELED])) {
                    return back()->with('error', 'همه پرسی مورد نظر به پایان رسیده است.');
                    
                }
                return back()->with('error', 'در حال حاضر این همه پرسی در دسترس نیست.');
            }

            if ($election->isExpired()) {
                return back()->with('error', 'زمان این همه‌پرسی به پایان رسیده است و امکان رای دادن وجود ندارد.');
            }

            $user = auth()->user();
            $isAdmin = $user && $user->hasRole(Role::Manager->value);

            $participant = Participant::where('user_id', auth()->id())
                ->where('event_id', $event->id)
                ->first();

            if (!$participant && $isAdmin) {
                $participant = Participant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'is_present' => true,
                    'normal_stock_count' => $election->normal_stock_count ?? 0,
                    'prefered_stock_count' => $election->prefered_stock_count ?? 0,
                ]);
            }

            if (!$participant) {
                return back()->with('error', 'شما در این رویداد شرکت کننده نیستید.');
            }

            $hasVoted = Vote::where('election_id', $election->id)
                ->where('participant_id', $participant->id)
                ->exists();

            if ($hasVoted) {
                return back()->with('error', 'رای شما قبلاً ثبت شده است. امکان ثبت رای مجدد در این رویداد وجود ندارد');
            }

            if (!$participant->is_present && !$isAdmin) {
                return back()->with('error', 'شما در جلسه حاضر نیستید و نمی‌توانید رای بدهید.');
            }

            // جلوگیری از ثبت رأی برای کاربران مسدود
            if ($election->blockedUsers()->where('users.id', $user->id)->exists()) {
                return back()->with('error', 'شما مجاز به شرکت در رأی‌گیری این انتخابات نیستید.');
            }

            $directorVotes = $request->input('director_candidates', []);

            \Log::info('Director votes received', [
                'director_votes' => $directorVotes,
                'count' => count($directorVotes),
            ]);

            if (empty($directorVotes)) {
                return back()->with('error', 'لطفاً حداقل به یک کاندیدا رای دهید.');
            }

            $totalVotesGiven = 0;
            $totalCandidates = $election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->count();
            $votedCandidatesCount = 0;

            DB::transaction(function () use ($election, $participant, $directorVotes, &$totalVotesGiven, $user, &$votedCandidatesCount) {
                
            $userVoteCount = $participant->normal_stock_count + ($participant->prefered_stock_count * $election->prefered_stock_weight);

                foreach ($directorVotes as $candidateId => $voteCount) {
                    $voteValue = is_numeric($voteCount) ? (float) $voteCount : (is_string($voteCount) && $voteCount === '1' ? 1 : 0);

                    if ($voteValue > $userVoteCount) { 
                        throw new Exception("شما نمی‌توانید بیش از {$userVoteCount} رای ثبت کنید.");
                    }

                    if ($voteValue > 0) {
                        $candidate = $election->candidates()->where('id', $candidateId)->first();
                        if (!$candidate) {
                            throw new \Exception("کاندیدای با شناسه {$candidateId} یافت نشد.");
                        }

                        $totalVotesGiven += $voteValue;
                        $votedCandidatesCount++;

                        $vote = Vote::create([
                            'election_id' => $election->id,
                            'participant_id' => $participant->id,
                            'candidate_id' => $candidateId,
                            'vote_count' => $voteValue,
                        ]);

                        \Log::info('Vote created', [
                            'vote_id' => $vote->id,
                            'election_id' => $election->id,
                            'participant_id' => $participant->id,
                            'candidate_id' => $candidateId,
                            'vote_count' => $voteValue,
                        ]);
                    }
                }
            });

            \Log::info('All votes stored successfully', [
                'total_votes_given' => $totalVotesGiven,
                'voted_candidates_count' => $votedCandidatesCount,
            ]);

            $notVotedCandidatesCount = $totalCandidates - $votedCandidatesCount;

            $totalAvailableStock = $participant->total_stock;
            $remainingStock = $totalAvailableStock - $totalVotesGiven;
                                    
        } catch (\Throwable $th) {
            Log::error('Error while storing votes', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return back()->with('error', 'خطا در ثبت رای: ' . $th->getMessage());
        }

        return to_route('my-elections.index', $group->slug)
                ->with('success', 'رأی شما با موفقیت ثبت شد.');
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

    public function terminate(Request $request, Group $group, Election $election)
    {
        try {
            $election->rounds->map(fn($round) => $round->update([
                'is_active' => false,
            ]));

            $election->update([
                'status' => ElectionStatus::COMPLETED,
            ]);

            return back()->with('success', __('messages.voting.terminated'));

        } catch (\Throwable $th) {
            Log::error('Error while terminating election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'user_id' => auth()->id(),
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return back()->with('error', __('messages.voting.terminate_error'));
        }
    }

    public function surveysIndex()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $participants = \App\Models\Participant::where('user_id', $user->id)
            ->with([
                'event.group',
                'event.elections' => function ($query) {
                    $query->where('status', \App\Enums\ElectionStatus::ONGOING)
                        ->where('type', \App\Enums\ElectionType::SURVEY->value)
                        ->with('candidates.user', 'position');
                }
            ])
            ->get();

        $availableElections = collect();

        foreach ($participants as $participant) {
            $event = $participant->event;
            if ($event && $event->elections) {
                foreach ($event->elections as $election) {
                    $hasVoted = \App\Models\Vote::where('election_id', $election->id)
                        ->where('participant_id', $participant->id)
                        ->exists();

                    if (!$hasVoted && $election->candidates->count() > 0) {
                        $availableElections->push([
                            'election' => $election,
                            'event' => $event,
                            'group' => $event->group,
                            'participant' => $participant,
                            'has_voted' => false,
                        ]);
                    }
                }
            }
        }

        return view('app.elections.my-elections', ['availableElections' => $availableElections, 'title' => 'نظرسنجی‌های من']);
    }

    public function liveStats(Event $event)
    {
        dd("da");
        $elections = $event->elections()
            ->where('status', ElectionStatus::ONGOING)
            ->with('votes')
            ->get();

        $totalStock = $event->participants()
            ->sum(\DB::raw('normal_stock_count + prefered_stock_count'));

        $data = [];

        foreach ($elections as $election) {

            $usedStock = $election->votes()->sum('vote_count');

            $percent = $totalStock > 0
                ? round(($usedStock / $totalStock) * 100, 2)
                : 0;

            $data[] = [
                'id' => $election->id,
                'title' => $election->title,
                'used_stock' => $usedStock,
                'total_stock' => $totalStock,
                'percent' => $percent,
            ];
        }

        return response()->json($data);
    }
}
