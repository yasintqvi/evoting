<?php

namespace App\Http\Controllers;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use App\Exports\ElectionReportExport;
use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Http\Resources\ElectionResource;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use App\Models\Position;
use App\Models\User;
use App\Models\Vote;
use App\Services\ElectionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ElectionController extends Controller
{
    private ElectionService $electionService;

    public function __construct(ElectionService $electionService)
    {
        $this->electionService = $electionService;
    }

    public function index(Request $request, Group $group, Event $event)
    {
        $elections = ElectionResource::collection($this->electionService->getAll($event))->toArray($request);

        return view('app.group.event.election.index', compact('group', 'elections', 'event'));
    }

    public function create(Group $group, Event $event)
    {
        $group->load('users');

        if ($group->type->value === \App\Enums\GroupType::SPECIAL->value) {
            $totalNormal = (int) $group->normal_stock_count;
            $totalPrefered = (int) $group->prefered_stock_count;

            $allocatedNormal = (int) $group->users->sum('pivot.normal_stock_count');
            $allocatedPrefered = (int) $group->users->sum('pivot.prefered_stock_count');

            $remainingNormal = $totalNormal - $allocatedNormal;
            $remainingPrefered = $totalPrefered - $allocatedPrefered;

            if ($remainingNormal !== 0 || $remainingPrefered !== 0) {
                return redirect()->back()
                    ->with('error', 'تمام سهام (عادی و ممتاز) باید تخصیص داده شود تا بتوانید همه‌پرسی ایجاد کنید.');
            }
        }

        $users = User::select('id', 'first_name', 'last_name')->get();
        $positions = Position::select('id', 'title')->get();

        return view('app.group.event.election.create', compact('group', 'event', 'users', 'positions'));
    }

    public function store(StoreElectionRequest $request, Group $group, Event $event): RedirectResponse
    {
        try {
            $group = $this->electionService->create($group, $event, $request->toDto());

            return to_route('elections.index', [$group, $event])->with('success', __('messages.election.created'));
        } catch (Throwable $th) {
            Log::error('Error creating election', [
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', __('messages.election.error'));
        }
    }

    public function show(Request $request, Group $group, Event $event, Election $election): View
    {
        $election = ElectionResource::make($election)->toArray($request);

        return view('app.group.event.election.show', compact('group', 'event', 'election'));
    }

    public function edit(Request $request, Group $group, Event $event, Election $election): View|RedirectResponse
    {
        // در صورتی که انتخاباتی در حال اجرا در این رویداد وجود داشته باشد، ویرایش مجاز نیست
        $hasOngoing = $event->elections()
            ->where('status', ElectionStatus::ONGOING)
            ->where('id', '!=', $election->id)
            ->exists();

        if ($hasOngoing) {
            return redirect()
                ->route('elections.index', [$group, $event])
                ->with('error', 'امکان ویرایش انتخابات در زمانی که انتخابات دیگری در حال برگزاری است وجود ندارد.');
        }

        $users = User::select('id', 'first_name', 'last_name')->get();

        $positions = Position::all();

        return view('app.group.event.election.edit', compact('group', 'event', 'users', 'election', 'positions'));
    }

    public function update(UpdateElectionRequest $request, Group $group, Event $event, Election $election): RedirectResponse
    {
        try {
            $this->electionService->update($election, $request->toDto());

            return to_route('elections.index', [$group, $event])->with('success', __('messages.election.edited'));
        } catch (Throwable $th) {
            Log::error('Error updating election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => $request->user()?->id,
            ]);

            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(Group $group, Event $event, Election $election): RedirectResponse
    {
        try {

            $this->electionService->delete($election);

            return back()->with('success', __('messages.election.deleted'));
        } catch (Throwable $th) {
            Log::error('Error deleting election', [
                'election_id' => $election->id,
                'group_id' => $group->id,
                'event_id' => $event->id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
                'performed_by' => auth()->id(),
            ]);

            return back()->with('error', $th->getMessage());
        }
    }

    public function start(Group $group, Event $event, Election $election)
    {
        try {
            if ($election->status !== ElectionStatus::WAITING_TO_START) {
                return back()->with('error', 'وضعیت فعلی انتخابات قابل شروع نیست.');
            }

            if ($election->type === ElectionType::SURVEY) {
                $election->update([
                    'status' => ElectionStatus::ONGOING,
                ]);

                return back()->with('success', 'نظرسنجی با موفقیت شروع شد.');
            }

            $event->load('participants');

            if ($election->type === ElectionType::PUBLIC_JOINT) {

                $presentCount = $event->participants->where('is_present', 1)->count();

                $totalMembers = $event->participants->count();

                $required = ($event->quorum_percent / 100) * $totalMembers;

                if ($presentCount < $required) {
                    return back()->with(
                        'error',
                        "تعداد افراد حاضر ({$presentCount}) به حد نصاب ({$required}) نرسیده است."
                    );
                }

            } else {

                $totalStocks = $group->normal_stock_count + $group->prefered_stock_count;

                $presentStock = $event->participants
                    ->where('is_present', 1)
                    ->sum(fn($p) => $p->normal_stock_count + $p->prefered_stock_count);

                $required = ($event->quorum_percent / 100) * $totalStocks;

                if ($presentStock < $required) {
                    return back()->with(
                        'error',
                        "سهام حاضرین ({$presentStock}) به حد نصاب ({$required}) نرسیده است."
                    );
                }
            }

            $election->update([
                'status' => ElectionStatus::ONGOING,
            ]);

            return back()->with('success', 'انتخابات با موفقیت شروع شد.');

        } catch (\Exception $e) {
            Log::error('Error starting election', [
                'election_id' => $election->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'خطایی در شروع انتخابات رخ داد.');
        }
    }

    public function end(Group $group, Event $event, Election $election)
    {
        try {
            if ($election->status !== ElectionStatus::ONGOING) {
                return back()->with('error', 'وضعیت فعلی انتخابات قابل شروع نیست.');
            }

            $election->status = ElectionStatus::COMPLETED;
            $election->save();

            return back()->with('success', 'انتخابات با موفقیت به پایان رسید و تمام انتخابات دیگر نیز بسته شدند.');
        } catch (\Exception $e) {
            Log::error('Error ending election', [
                'election_id' => $election->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'خطایی در پایان انتخابات رخ داد.');
        }
    }

    // public function report(Request $request, Group $group, Event $event, Election $election): View
    // {
    //     $votes = Vote::where('election_id', $election->id)
    //         ->with(['candidate.user', 'participant.user'])
    //         ->get();

    //     $candidateVotes = $votes->groupBy('candidate_id')->map(function ($candidateVotes) {
    //         return [
    //             'candidate' => $candidateVotes->first()->candidate,
    //             'total_votes' => $candidateVotes->sum('vote_count'),
    //             'vote_count' => $candidateVotes->count(),
    //         ];
    //     })->sortByDesc('total_votes')->values();

    //     $totalVotes = $votes->sum('vote_count');
    //     $totalParticipants = $votes->groupBy('participant_id')->count();
    //     $totalCandidates = $candidateVotes->count();

    //     return view('app.group.event.election.report', compact(
    //         'group',
    //         'event',
    //         'election',
    //         'candidateVotes',
    //         'totalVotes',
    //         'totalParticipants',
    //         'totalCandidates'
    //     ));
    // }

    public function report(Request $request, Group $group, Event $event, Election $election): View|\Illuminate\Http\Response|BinaryFileResponse
    {
        $electionVotes = $election->votes;

        $candidateVotes = $election->candidates()
            ->with(['user'])
            ->withSum('votes', 'vote_count')
            ->orderByDesc('votes_sum_vote_count')
            ->get();

        $totalVotes = $electionVotes->sum('vote_count');
        $totalParticipants = $electionVotes->groupBy('participant_id')->count();
        $totalCandidates = $candidateVotes->count();

        if ($request->has('download_pdf')) {
            $pdf = Pdf::loadView('app.group.event.election.report-pdf', compact(
                'group',
                'event',
                'election',
                'candidateVotes',
                'totalVotes',
                'totalParticipants',
                'totalCandidates'
            ), [], [
                'format' => 'A4-L',
                'orientation' => 'L',
            ]);

            return $pdf->download('election-report-' . $election->id . '.pdf');
        }

        if ($request->has('download_excel')) {
            $export = new ElectionReportExport(
                $group->title ?? ($group->name ?? ''),
                $event->title ?? ($event->name ?? ''),
                $election->title ?? '',
                $totalVotes,
                $totalParticipants,
                $totalCandidates,
                $candidateVotes
            );

            return Excel::download($export, 'election-report-' . $election->id . '.xlsx');
        }

        // نمایش عادی در مرورگر
        return view('app.group.event.election.report', compact(
            'group',
            'event',
            'election',
            'candidateVotes',
            'totalParticipants',
            'totalCandidates',
            'totalVotes'
        ));
    }

    public function detailedReport(Request $request, Group $group, Event $event, Election $election): View
    {
        $votes = Vote::where('election_id', $election->id)
            ->with(['participant.user', 'candidate.user'])
            ->get();

        // ۱. محاسبه مود (نما) سهام‌ها
        $voteCounts = $votes->pluck('vote_count')->filter()->toArray();

        // مقدار پیش‌فرض برای مود (اگر داده‌ای نبود یا همه یکسان بودند)
        // این عدد تعیین می‌کند هر سطر حداکثر چند رای داشته باشد
        $mode = 10;

        if (!empty($voteCounts)) {
            $frequencies = array_count_values($voteCounts);
            arsort($frequencies);
            $mode = array_key_first($frequencies);
        }

        $detailedVotes = collect();

        foreach ($votes as $vote) {
            $user = $vote->participant?->user;
            $nationalCode = $user?->nationalcode ?? '---';

            // ۲. ماسک کردن کد ملی
            if ($nationalCode !== '---' && mb_strlen($nationalCode) >= 4) {
                $end = mb_substr($nationalCode, -4);
                $maskedCode = '******' . $end;
            } else {
                $maskedCode = '****';
            }

            $candidateName = $vote->candidate?->user?->full_name ?? '---';
            $totalShares = $vote->vote_count;

            // ۳. شکستن سهام‌ها (Chunking) بر اساس مود محاسبه شده
            $remainingShares = $totalShares;

            while ($remainingShares > 0) {
                // اینجا حتما از متغیر $mode استفاده می‌شود
                $chunk = min($remainingShares, $mode);

                $detailedVotes->push([
                    'masked_national_code' => $maskedCode,
                    'candidate_name' => $candidateName,
                    'vote_chunk' => $chunk,
                ]);

                $remainingShares -= $chunk;
            }
        }


        // ۴. بر زدن (Shuffle)
        $detailedVotes = $detailedVotes->shuffle();

        return view('app.group.event.election.detailed-report', compact(
            'group',
            'event',
            'election',
            'detailedVotes',
            'mode'
        ));
    }

    // public function detailedReport(Request $request, Group $group, Event $event, Election $election): View
    // {
    //     $votes = Vote::where('election_id', $election->id)
    //         ->with(['participant.user', 'candidate.user'])
    //         ->get();

    //     // استفاده از GroupBy برای دسته‌بندی آرا بر اساس کد ملی رای‌دهنده
    //     $groupedVotes = $votes->groupBy(function ($vote) {
    //         return $vote->participant?->user?->nationalcode ?? 'unknown';
    //     });

    //     $detailedVotes = collect();

    //     foreach ($groupedVotes as $nationalCode => $userVotes) {
    //         // ۱. ماسک کردن کد ملی
    //         if ($nationalCode !== 'unknown' && mb_strlen($nationalCode) >= 4) {
    //             $end = mb_substr($nationalCode, -4);
    //             $maskedCode = '******' . $end;
    //         } else {
    //             $maskedCode = '****';
    //         }

    //         // ۲. محاسبه مجموع آرا برای هر کاندیدا توسط این فرد خاص
    //         // ما دوباره آرا را بر اساس کاندیدا گروه‌بندی می‌کنیم
    //         $votesByCandidate = $userVotes->groupBy(function ($vote) {
    //             return $vote->candidate?->user?->full_name ?? '---';
    //         });

    //         foreach ($votesByCandidate as $candidateName => $candidateVotes) {
    //             // جمع زدن تعداد آرا
    //             $totalVotesForCandidate = $candidateVotes->sum('vote_count');

    //             $detailedVotes->push([
    //                 'masked_national_code' => $maskedCode,
    //                 'candidate_name' => $candidateName,
    //                 'vote_chunk' => $totalVotesForCandidate, // مجموع آرا
    //             ]);
    //         }
    //     }

    //     // نکته: در اینجا دیگر Shuffle نمی‌کنیم تا ترتیب برای بررسی حفظ شود
    //     // اما اگر می‌خواهید کد ملی‌ها قاطی باشند، می‌توانید خط زیر را فعال کنید:
    //     // $detailedVotes = $detailedVotes->shuffle();

    //     return view('app.group.event.election.detailed-report', compact(
    //         'group',
    //         'event',
    //         'election',
    //         'detailedVotes',
    //         // 'mode' // در این نسخه متغیر mode استفاده نمی‌شود اما برای جلوگیری از ارور ارسال می‌شود
    //     ));
    // }
}
