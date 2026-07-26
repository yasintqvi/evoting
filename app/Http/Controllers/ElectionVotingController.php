<?php

namespace App\Http\Controllers;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use App\Enums\Role;
use App\Http\Requests\Election\StoreVotingRequest;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Participant;
use App\Models\User;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionVotingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $user = User::query()->find(auth()->id());

    //     if (!$user) {
    //         return redirect()->route('login');
    //     }

    //     $hiddenPastElectionIds = collect((array) data_get($user->meta ?? [], 'hidden_past_election_ids', []))
    //         ->map(fn($id) => (int) $id)
    //         ->filter()
    //         ->unique()
    //         ->values();

    //     $participants = Participant::where('user_id', $user->id)
    //         ->where('is_present', true)
    //         ->whereNull('attorney_id')
    //         ->with([
    //             'event.group',
    //             'event.elections' => function ($query) {
    //                 $query->whereIn('status', [
    //                     ElectionStatus::ONGOING,
    //                     ElectionStatus::COMPLETED,
    //                     ElectionStatus::CANCELED,
    //                 ])->with('candidates.user', 'position');
    //             },
    //             'votes',
    //         ])
    //         ->get();

    //     $availableElections = collect();
    //     $unavailableElections = collect();

    //     foreach ($participants as $participant) {
    //         $event = $participant->event;
    //         if ($event && $event->elections) {
    //             foreach ($event->elections as $election) {
    //                 if ($this->effectiveVotePower($election, $event, $user) <= 0) {
    //                     continue;
    //                 }

    //                 if ($election->status === ElectionStatus::ONGOING) {
    //                     $hasVoted = Vote::where('election_id', $election->id)
    //                         ->where('participant_id', $participant->id)
    //                         ->exists();

    //                     if (!$hasVoted && ($election->candidates->count() > 0 || $election->type === ElectionType::SURVEY)) {
    //                         $availableElections->push([
    //                             'election' => $election,
    //                             'event' => $event,
    //                             'group' => $event->group,
    //                             'participant' => $participant,
    //                             'has_voted' => false,
    //                         ]);
    //                     }
    //                 }

    //                 if ($election->isFinished()) {
    //                     if (!$hiddenPastElectionIds->contains((int) $election->id)) {
    //                         $unavailableElections->push([
    //                             'election' => $election,
    //                             'event' => $event,
    //                             'group' => $event->group,
    //                             'participant' => $participant,
    //                         ]);
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //     return view('app.elections.my-elections', [
    //         'availableElections' => $availableElections,
    //         'unavailableElections' => $unavailableElections,
    //         'title' => 'انتخابات من',
    //     ]);
    // }

    public function index(Request $request)
    {
        $user = User::query()->find(auth()->id());

        if (!$user) {
            return redirect()->route('login');
        }

        $hiddenPastElectionIds = collect((array) data_get($user->meta ?? [], 'hidden_past_election_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $participants = Participant::where('user_id', $user->id)
            ->where('is_present', true)
            ->whereNull('attorney_id')
            ->with([
                'event.group',
                'event.elections' => function ($query) {
                    $query->whereIn('status', [
                        ElectionStatus::ONGOING,
                        ElectionStatus::COMPLETED,
                        ElectionStatus::CANCELED,
                    ])->with('candidates.user', 'position');
                },
                'votes',
                'is_attorney',
            ])
            ->get();

        // گرفتن فیلترها از درخواست
        $statusFilter = $request->input('status');
        $startDate = $request->input('start_date') ? convert_persian_to_english($request->input('start_date')) : null;
        $endDate = $request->input('end_date') ? convert_persian_to_english($request->input('end_date')) : null;

        $availableElections = collect();
        $unavailableElections = collect();

        foreach ($participants as $participant) {
            $event = $participant->event;
            if ($event && $event->elections) {
                foreach ($event->elections as $election) {
                    if ($this->effectiveVotePower($election, $event, $user) <= 0) {
                        continue;
                    }

                    if (! $event->userCanParticipateInVoting((int) $user->id, $election)) {
                        continue;
                    }

                    // اعمال فیلتر تاریخ
                    if ($startDate || $endDate) {
                        $electionDate = $election->created_at;

                        if ($startDate) {
                            $startTimestamp = verta($startDate)->toCarbon()->startOfDay();
                            if ($electionDate < $startTimestamp) {
                                continue;
                            }
                        }

                        if ($endDate) {
                            $endTimestamp = verta($endDate)->toCarbon()->endOfDay();
                            if ($electionDate > $endTimestamp) {
                                continue;
                            }
                        }
                    }

                    // انتخابات در حال برگزاری
                    if ($election->status === ElectionStatus::ONGOING) {
                        // اعمال فیلتر وضعیت
                        if ($statusFilter && $statusFilter !== 'ongoing') {
                            continue;
                        }

                        // بررسی انقضا
                        if ($election->isExpired()) {
                            if ($statusFilter === 'expired') {
                                $unavailableElections->push([
                                    'election' => $election,
                                    'event' => $event,
                                    'group' => $event->group,
                                    'participant' => $participant,
                                    'status_text' => 'منقضی شده',
                                    'status_type' => 'expired'
                                ]);
                            }
                            continue;
                        }

                        $hasVoted = Vote::where('election_id', $election->id)
                            ->where('participant_id', $participant->id)
                            ->exists();

                        if (!$hasVoted && ($election->candidates->count() > 0 || $election->type === ElectionType::SURVEY)) {
                            $availableElections->push([
                                'election' => $election,
                                'event' => $event,
                                'group' => $event->group,
                                'participant' => $participant,
                                'has_voted' => false,
                                'status_text' => 'در حال برگزاری',
                                'status_type' => 'ongoing'
                            ]);
                        }
                    }

                    // انتخابات تمام شده یا لغو شده
                    // وکیل (کسی که به او وکالت داده شده) نباید انتخابات گذشته را ببیند.
                    if ($election->isFinished()) {
                        if ($participant->is_attorney->isNotEmpty()) {
                            continue;
                        }

                        if (!$hiddenPastElectionIds->contains((int) $election->id)) {
                            // اعمال فیلتر وضعیت
                            if ($statusFilter) {
                                if ($statusFilter === 'completed' && $election->status !== ElectionStatus::COMPLETED) {
                                    continue;
                                }
                                if ($statusFilter === 'canceled' && $election->status !== ElectionStatus::CANCELED) {
                                    continue;
                                }
                                if ($statusFilter === 'expired' && !$election->isExpired()) {
                                    continue;
                                }
                                if (!in_array($statusFilter, ['completed', 'canceled', 'expired'])) {
                                    continue;
                                }
                            }

                            $statusText = '';
                            $statusType = '';

                            if ($election->isExpired()) {
                                $statusText = 'منقضی شده';
                                $statusType = 'expired';
                            } elseif ($election->status === ElectionStatus::COMPLETED) {
                                $statusText = 'تمام شده';
                                $statusType = 'completed';
                            } elseif ($election->status === ElectionStatus::CANCELED) {
                                $statusText = 'لغو شده';
                                $statusType = 'canceled';
                            }

                            $unavailableElections->push([
                                'election' => $election,
                                'event' => $event,
                                'group' => $event->group,
                                'participant' => $participant,
                                'status_text' => $statusText,
                                'status_type' => $statusType
                            ]);
                        }
                    }
                }
            }
        }

        // مرتب‌سازی بر اساس تاریخ (جدیدترین اولویت)
        $availableElections = $availableElections->sortByDesc(function ($item) {
            return $item['election']->created_at;
        })->values();

        $unavailableElections = $unavailableElections->sortByDesc(function ($item) {
            return $item['election']->created_at;
        })->values();

        return view('app.elections.my-elections', [
            'availableElections' => $availableElections,
            'unavailableElections' => $unavailableElections,
            'title' => 'انتخابات من',
        ]);
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

        $participant = $this->votableParticipantForEvent($event, (int) $user->id);

        if (!$participant && $isAdmin) {
            $participant = Participant::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'group_id' => $group->id,
                'is_present' => true,
                'attorney_id' => null,
                'normal_stock_count' => $election->normal_stock_count ?? 0,
                'prefered_stock_count' => $election->prefered_stock_count ?? 0,
            ]);
        }

        if (!$participant) {
            return back()->with('error', 'شما برای این رویداد وکالت داده‌اید؛ رأی‌گیری فقط توسط وکیل شما انجام می‌شود.');
        }

        if (! $event->userCanParticipateInVoting((int) $user->id, $election)) {
            return back()->with('error', 'به دلیل نداشتن سهام، امکان شرکت در رأی‌گیری ندارید.');
        }

        // جلوگیری از شرکت کاربرانی که مسدود شده‌اند
        if ($election->blockedUsers()->wherePivot('user_id', $user->id)->exists()) {
            return back()->with('error', 'شما مجاز به شرکت در رأی‌گیری این انتخابات نیستید.');
        }

        $votableParticipantIds = $this->votableParticipantIdsForEvent($event, (int) $user->id);

        $hasVoted = Vote::where('election_id', $election->id)
            ->whereIn('participant_id', $votableParticipantIds)
            ->exists();

        if ($hasVoted) {
            return back()->with('error', 'شما قبلاً در این انتخابات رای داده‌اید و نمی‌توانید دوباره رای بدهید.');
        }

        $election->load('candidates');

        // وکیلِ بدون سهام شخصی شمرده نمی‌شود؛ وکیلِ سهام‌دار (مثل خودش حاضر) می‌ماند.
        $attendanceParticipantsQuery = $event->participants()
            ->visibleForAttendance($group)
            ->whereNotIn('id', $event->attorneyOnlyParticipantIds());

        $presentParticipantsCount = (int) (clone $attendanceParticipantsQuery)
            ->where('is_present', true)
            ->count();

        $totalParticipantsInEvent = (int) (clone $attendanceParticipantsQuery)->count();
        $absentParticipantsCount = max(0, $totalParticipantsInEvent - $presentParticipantsCount);

        $userParticipantIds = Participant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->whereNull('attorney_id')
            ->pluck('id');

        $representedParticipants = $userParticipantIds->isNotEmpty()
            ? Participant::where('event_id', $event->id)
                ->whereIn('attorney_id', $userParticipantIds)
                ->with('user')
                ->get()
            : collect();


        $effectiveStock = $this->effectiveVotePower($election, $event, $user);

        $directorCandidateCount = (int) $election->candidates
            ->where('candidate_type', CandidateType::DIRECTOR)
            ->count();

        // سهام موکل بعد از وکالت نزد وکیل است؛ برای مجموع سهام، وکیل را نگه می‌داریم.
        $allParticipantsInEvent = $event->participants()
            ->visibleForAttendance($group)
            ->where('is_present', true)
            ->get();

        $totalEffectiveStockOfAllParticipants = 0;
        foreach ($allParticipantsInEvent as $allParticipant) {
            $totalEffectiveStockOfAllParticipants += $this->participantElectionStockUnits($election, $allParticipant);
        }

        // سقف کل آرای گروه (مجموع سهام مؤثر همه حاضرین × تعداد اعضای اصلی)
        $totalArticle88VotePool = $election->type === ElectionType::PRIVATE_JOINT_WITH_88
            ? (float) $totalEffectiveStockOfAllParticipants * (float) max(0, (int) $election->main_member_count)
            : 0.0;

        // سهم شخصی شما
        $yourEffectiveStock = $effectiveStock;
        // ========================================================

        $article88VotePool = $election->type === ElectionType::PRIVATE_JOINT_WITH_88
            ? (float) $effectiveStock * (float) max(0, (int) $election->main_member_count)
            : 0.0;



        return view('app.group.event.election.voting.create', compact(
            'group',
            'event',
            'election',
            'participant',
            'effectiveStock',
            'directorCandidateCount',
            'article88VotePool',
            'presentParticipantsCount',
            'totalParticipantsInEvent',
            'absentParticipantsCount',
            'representedParticipants',
            'totalEffectiveStockOfAllParticipants',
            'totalArticle88VotePool',
            'yourEffectiveStock',
        ));
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

            $participant = $this->votableParticipantForEvent($event, (int) $user->id);

            if (!$participant && $isAdmin) {
                $participant = Participant::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'group_id' => $group->id,
                    'is_present' => true,
                    'attorney_id' => null,
                    'normal_stock_count' => $election->normal_stock_count ?? 0,
                    'prefered_stock_count' => $election->prefered_stock_count ?? 0,
                ]);
            }

            if (!$participant) {
                return back()->with('error', 'شما برای این رویداد وکالت داده‌اید؛ رأی‌گیری فقط توسط وکیل شما انجام می‌شود.');
            }

            if (! $event->userCanParticipateInVoting((int) $user->id, $election)) {
                return back()->with('error', 'به دلیل نداشتن سهام، امکان شرکت در رأی‌گیری ندارید.');
            }

            $votableParticipantIds = $this->votableParticipantIdsForEvent($event, (int) $user->id);

            $hasVoted = Vote::where('election_id', $election->id)
                ->whereIn('participant_id', $votableParticipantIds)
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
            if (!is_array($directorVotes)) {
                $directorVotes = [];
            }

            \Log::info('Director votes received', [
                'director_votes' => $directorVotes,
                'count' => count($directorVotes),
            ]);

            $totalVotesGiven = 0;
            $totalCandidates = $election->candidates()->where('candidate_type', CandidateType::DIRECTOR)->count();
            $votedCandidatesCount = 0;
            $isAbstain = false;

            DB::transaction(function () use ($election, $event, $participant, $directorVotes, &$totalVotesGiven, $user, &$votedCandidatesCount, &$isAbstain) {
                $userVotePower = $this->effectiveVotePower($election, $event, $user);
                $article88MaxPool = $election->type === ElectionType::PRIVATE_JOINT_WITH_88
                    ? $userVotePower * max(0, (int) $election->main_member_count)
                    : null;

                $normalized = [];
                foreach ($directorVotes as $candidateId => $voteCount) {
                    $voteValue = $this->parseIncomingVoteCount($voteCount);
                    if ($voteValue <= 0) {
                        continue;
                    }

                    $candidate = $election->candidates()->where('id', $candidateId)->first();
                    if (!$candidate) {
                        throw new Exception("کاندیدای با شناسه {$candidateId} یافت نشد.");
                    }

                    if ($election->type === ElectionType::PRIVATE_JOINT_WITH_88) {
                        if ($article88MaxPool !== null && $voteValue > $article88MaxPool) {
                            throw new Exception("به هر نامزد حداکثر {$article88MaxPool} رأی می‌توانید اختصاص دهید.");
                        }
                    } elseif ($voteValue > $userVotePower) {
                        throw new Exception("شما نمی‌توانید بیش از {$userVotePower} رای ثبت کنید.");
                    }

                    $normalized[(int) $candidateId] = $voteValue;
                }

                $mainSeatCount = (int) ($election->main_member_count ?? 0);
                if ($mainSeatCount > 0 && count($normalized) > $mainSeatCount) {
                    throw new Exception("شما نمی‌توانید به بیش از {$mainSeatCount} کاندیدا رأی دهید.");
                }

                // شرکت در رأی‌گیری بدون انتخاب کاندیدا (ممتنع)
                if ($normalized === []) {
                    $isAbstain = true;
                    Vote::create([
                        'election_id' => $election->id,
                        'participant_id' => $participant->id,
                        'candidate_id' => null,
                        'vote_count' => 0,
                    ]);

                    return;
                }

                $totalVotesGiven = array_sum($normalized);

                if ($election->type === ElectionType::PRIVATE_JOINT_WITH_88 && $article88MaxPool !== null) {
                    if ($totalVotesGiven > $article88MaxPool) {
                        throw new Exception('مجموع آرا نمی‌تواند بیش از ' . (string) $article88MaxPool . ' (سهم شما × تعداد نامزدها) باشد.');
                    }
                }

                foreach ($normalized as $candidateId => $voteValue) {
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
            });

            \Log::info('All votes stored successfully', [
                'total_votes_given' => $totalVotesGiven,
                'voted_candidates_count' => $votedCandidatesCount,
                'is_abstain' => $isAbstain,
            ]);

            $notVotedCandidatesCount = $totalCandidates - $votedCandidatesCount;

            $userVotePowerAfter = $this->effectiveVotePower($election, $event, $user);
            if ($election->type === ElectionType::PRIVATE_JOINT_WITH_88) {
                $totalAvailableStock = $userVotePowerAfter * max(0, (int) $election->main_member_count);
            } else {
                $totalAvailableStock = (float) $participant->total_stock;
            }
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

        $successMessage = !empty($isAbstain)
            ? 'حضور شما در رأی‌گیری ثبت شد (بدون رأی به کاندیدا).'
            : 'رأی شما با موفقیت ثبت شد.';

        return to_route('my-elections.index', $group->slug)
            ->with('success', $successMessage);
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
    public function destroy(Request $request, Election $election)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!$election->isFinished()) {
            return redirect()
                ->route('my-elections.index')
                ->with('error', 'فقط انتخابات پایان‌یافته را می‌توانید از لیست خود حذف کنید.');
        }

        $user = User::query()->findOrFail(auth()->id());

        $isParticipant = Participant::query()
            ->where('user_id', $user->id)
            ->where('event_id', $election->event_id)
            ->exists();

        if (!$isParticipant) {
            return redirect()
                ->route('my-elections.index')
                ->with('error', 'شما به این انتخابات دسترسی ندارید.');
        }

        $meta = is_array($user->meta) ? $user->meta : [];
        $hidden = (array) data_get($meta, 'hidden_past_election_ids', []);
        $hidden[] = (int) $election->id;
        $meta['hidden_past_election_ids'] = collect($hidden)
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $user->fill(['meta' => $meta]);
        $user->save();

        return redirect()
            ->route('my-elections.index')
            ->with('success', 'انتخابات از لیست شما حذف شد.');
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

        $now = now();

        $participants = Participant::where('user_id', $user->id)
            ->where('is_present', true)
            ->whereNull('attorney_id')
            ->with(['event.group', 'event.surveys.questions'])
            ->get();

        $availableSurveys = collect();

        foreach ($participants as $participant) {
            $event = $participant->event;
            if (!$event) {
                continue;
            }

            if (! $event->userCanParticipateInVoting((int) $user->id)) {
                continue;
            }

            foreach ($event->surveys->where('status', 1) as $survey) {
                if (
                    ($survey->start_at && $now->lt($survey->start_at)) ||
                    ($survey->end_at && $now->gt($survey->end_at))
                ) {
                    continue;
                }

                $hasAnswered = $survey->responses()
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$hasAnswered) {
                    $availableSurveys->push([
                        'survey' => $survey,
                        'event' => $event,
                        'group' => $event->group,
                        'participant' => $participant,
                        'has_answered' => false,
                    ]);
                }
            }
        }

        return view('app.surveys.my-surveys', [
            'availableSurveys' => $availableSurveys,
        ]);
    }

    public function liveStats(Event $event)
    {
        dd('da');
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

    /**
     * رکورد شرکت‌کننده‌ای که کاربر می‌تواند با آن رأی بدهد (بدون وکالت به دیگری).
     *
     * اگر چند رکورد برای همان کاربر و رویداد وجود داشته باشد (مثلاً بعد از لغو وکالت رکورد وکیل با سهام صفر
     * مانده و رکورد اصلی سهام را دارد)، رکوردی انتخاب می‌شود که بیشترین سهام مؤثر را دارد نه فقط جدیدترین id.
     */
    protected function votableParticipantForEvent(Event $event, int $userId): ?Participant
    {
        $participants = Participant::query()
            ->where('event_id', $event->id)
            ->where('user_id', $userId)
            ->whereNull('attorney_id')
            ->with('event.group')
            ->get();

        if ($participants->isEmpty()) {
            return null;
        }

        if ($participants->count() === 1) {
            $chosen = $participants->first();
            $chosen->loadMissing('event.group');

            return $chosen;
        }

        $chosen = $participants->sort(function (Participant $a, Participant $b) {
            $stockCmp = (float) $b->total_stock <=> (float) $a->total_stock;

            if ($stockCmp !== 0) {
                return $stockCmp;
            }

            return (int) $b->id <=> (int) $a->id;
        })->first();
        $chosen->loadMissing('event.group');

        return $chosen;
    }

    /**
     * @return Collection<int, int>
     */
    protected function votableParticipantIdsForEvent(Event $event, int $userId)
    {
        return Participant::query()
            ->where('event_id', $event->id)
            ->where('user_id', $userId)
            ->whereNull('attorney_id')
            ->pluck('id');
    }

    /**
     * قدرت رأی مؤثر برای این انتخابات (شامل وکالت، نوع انتخابات، وزن سهم ممتاز و ignore_stock_weight).
     */
    protected function effectiveVotePower(Election $election, Event $event, User $user): float
    {
        $userParticipants = Participant::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->whereNull('attorney_id')
            ->get();

        if ($userParticipants->isEmpty()) {
            return 0.0;
        }

        $userParticipantIds = $userParticipants->pluck('id');

        $representedParticipants = Participant::where('event_id', $event->id)
            ->whereIn('attorney_id', $userParticipantIds)
            ->get();

        if ($election->type === ElectionType::PUBLIC_JOINT) {
            return (float) ($userParticipants->count() + $representedParticipants->count());
        }

        $stockFn = fn(Participant $p) => $this->participantElectionStockUnits($election, $p);

        return (float) $userParticipants->sum($stockFn) + (float) $representedParticipants->sum($stockFn);
    }

    // protected function participantElectionStockUnits(Election $election, Participant $participant): float
    // {
    //     if ($election->ignore_stock_weight) {
    //         return (float) $participant->normal_stock_count + (float) $participant->prefered_stock_count;
    //     }

    //     return (float) $participant->normal_stock_count + ((float) $participant->prefered_stock_count * (float) $election->prefered_stock_weight);
    // }

    protected function participantElectionStockUnits(Election $election, Participant $participant): float
    {
        if ($election->ignore_stock_weight) {
            return (float) $participant->normal_stock_count + (float) $participant->prefered_stock_count;
        }

        return (float) $participant->normal_stock_count +
            ((float) $participant->prefered_stock_count * (float) $election->prefered_stock_weight);
    }

    /**
     * تبدیل ارقام فارسی/عربی و حذف جداکننده‌ها؛ ورودی‌هایی که با صفحه‌کلید فارسی وارد شده‌اند را هم می‌پذیرد.
     */
    protected function parseIncomingVoteCount(mixed $raw): float
    {
        if ($raw === null || $raw === '') {
            return 0.0;
        }
        if (is_bool($raw)) {
            return 0.0;
        }
        if (is_int($raw) || is_float($raw)) {
            return (float) $raw;
        }
        $s = trim((string) $raw);
        if ($s === '') {
            return 0.0;
        }
        $s = strtr($s, [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
        ]);
        $s = str_replace([',', '،', '٬', ' '], '', $s);
        $s = preg_replace('/[^\d.\-]/', '', $s) ?? '';
        if ($s === '' || $s === '.' || $s === '-' || !is_numeric($s)) {
            return 0.0;
        }

        return (float) $s;
    }
}
