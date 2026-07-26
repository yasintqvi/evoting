<?php

namespace App\Services;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Models\Election;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ElectionRunoffService
{
    /**
     * تشخیص تساوی در مرز کرسی اصلی / علی‌البدل (یا علی‌البدل / خارج).
     *
     * @return array{
     *     has_tie: bool,
     *     candidates: Collection<int, object>,
     *     contested_main_seats: int,
     *     contested_substitute_seats: int,
     *     tie_vote_count: int|null,
     *     message: string|null
     * }
     */
    public function analyzeTieBreak(Election $election): array
    {
        $mainSeats = max(0, (int) $election->main_member_count);
        $subSeats = max(0, (int) $election->substitute_member_count);

        $ranked = $election->candidates()
            ->with('user')
            ->withSum('votes', 'vote_count')
            ->orderByDesc('votes_sum_vote_count')
            ->orderBy('id')
            ->get()
            ->values();

        $empty = [
            'has_tie' => false,
            'candidates' => collect(),
            'contested_main_seats' => 0,
            'contested_substitute_seats' => 0,
            'tie_vote_count' => null,
            'message' => null,
        ];

        if ($ranked->isEmpty() || ($mainSeats + $subSeats) <= 0) {
            return $empty;
        }

        $boundaries = [];
        if ($mainSeats > 0 && $ranked->count() > $mainSeats) {
            $boundaries[] = $mainSeats;
        }
        if ($subSeats > 0 && $ranked->count() > ($mainSeats + $subSeats)) {
            $boundaries[] = $mainSeats + $subSeats;
        }

        $tieVote = null;
        foreach ($boundaries as $boundary) {
            $insideVotes = (int) ($ranked[$boundary - 1]->votes_sum_vote_count ?? 0);
            $outsideVotes = (int) ($ranked[$boundary]->votes_sum_vote_count ?? 0);
            if ($insideVotes === $outsideVotes) {
                $tieVote = $insideVotes;
                break;
            }
        }

        if ($tieVote === null) {
            return $empty;
        }

        $tied = $ranked->filter(
            fn ($candidate) => (int) ($candidate->votes_sum_vote_count ?? 0) === $tieVote
        )->values();

        if ($tied->count() < 2) {
            return $empty;
        }

        $tiedIndexes = $ranked->keys()->filter(
            fn ($i) => (int) ($ranked[$i]->votes_sum_vote_count ?? 0) === $tieVote
        );
        $firstTiedIndex = (int) $tiedIndexes->first();
        $lastTiedIndex = (int) $tiedIndexes->last();

        $contestedMain = 0;
        $contestedSub = 0;
        for ($i = $firstTiedIndex; $i <= $lastTiedIndex; $i++) {
            if ($i < $mainSeats) {
                $contestedMain++;
            } elseif ($i < $mainSeats + $subSeats) {
                $contestedSub++;
            }
        }

        // حداقل یک کرسی مورد اختلاف باید باشد تا دور دوم معنا داشته باشد.
        if (($contestedMain + $contestedSub) <= 0) {
            return $empty;
        }

        $names = $tied->map(fn ($c) => $c->user?->full_name)->filter()->implode('، ');

        return [
            'has_tie' => true,
            'candidates' => $tied,
            'contested_main_seats' => $contestedMain,
            'contested_substitute_seats' => $contestedSub,
            'tie_vote_count' => $tieVote,
            'message' => "تساوی آرا ({$tieVote}) بین {$names} در مرز اصلی/علی‌البدل؛ برای تعیین نهایی باید دور دوم برگزار شود.",
        ];
    }

    public function existingRunoff(Election $election): ?Election
    {
        return Election::query()
            ->where('parent_election_id', $election->id)
            ->latest('id')
            ->first();
    }

    /**
     * ایجاد انتخابات دور دوم فقط با کاندیداهای دارای رأی مساوی.
     */
    public function createRunoff(Election $parent, ?User $owner = null): Election
    {
        if ($parent->parent_election_id) {
            throw new Exception('برای انتخابات دور دوم نمی‌توان دوباره دور دوم ساخت.');
        }

        if ($parent->status !== ElectionStatus::COMPLETED) {
            throw new Exception('فقط پس از پایان انتخابات اصلی می‌توان دور دوم را ایجاد کرد.');
        }

        $existing = $this->existingRunoff($parent);
        if ($existing) {
            return $existing;
        }

        $analysis = $this->analyzeTieBreak($parent);
        if (! $analysis['has_tie']) {
            throw new Exception('تساوی قابل‌حل با دور دوم در نتایج فعلی وجود ندارد.');
        }

        return DB::transaction(function () use ($parent, $owner, $analysis) {
            $contestedMain = max(0, (int) $analysis['contested_main_seats']);
            $contestedSub = max(0, (int) $analysis['contested_substitute_seats']);

            // اگر فقط کرسی علی‌البدل محل اختلاف است، همان را به‌عنوان ظرفیت انتخاب در دور دوم بگذار.
            if ($contestedMain === 0 && $contestedSub > 0) {
                $contestedMain = $contestedSub;
                $contestedSub = 0;
            }

            $title = 'دور دوم — '.$parent->title.' (تعیین اصلی و علی‌البدل)';

            $runoff = Election::create([
                'event_id' => $parent->event_id,
                'parent_election_id' => $parent->id,
                'position_id' => $parent->position_id,
                'owner_id' => $owner?->id ?? $parent->owner_id,
                'title' => $title,
                'type' => $parent->type,
                'status' => ElectionStatus::WAITING_TO_START,
                'candidate_count' => $analysis['candidates']->count(),
                'normal_stock_count' => $parent->normal_stock_count,
                'prefered_stock_count' => $parent->prefered_stock_count,
                'prefered_stock_weight' => $parent->prefered_stock_weight,
                'main_member_count' => $contestedMain,
                'substitute_member_count' => $contestedSub,
                'ignore_stock_weight' => $parent->ignore_stock_weight,
            ]);

            foreach ($analysis['candidates'] as $candidate) {
                $runoff->candidates()->create([
                    'user_id' => $candidate->user_id,
                    'candidate_type' => CandidateType::DIRECTOR,
                ]);
            }

            $blockedIds = $parent->blockedUsers()->pluck('users.id')->all();
            if ($blockedIds !== []) {
                $runoff->blockedUsers()->sync($blockedIds);
            }

            return $runoff->fresh(['candidates.user']);
        });
    }
}
