<?php

namespace App\Providers;

use App\Enums\ElectionStatus;
use App\Models\Participant;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('app.*', function ($view) {
            $user = auth()->user();

            $participants = Participant::where('user_id', $user->id)
                ->where('is_present', true)
                ->whereNull('attorney_id')
                ->with([
                    'event.group',
                    'event.elections.candidates.user',
                    'event.elections.position',
                    'event.surveys.questions',
                    'is_attorney',
                ])
                ->get();

            $now = now();
            $sidebarAvailableElections = collect();
            $sidebarUnavailableElections = collect();
            $sidebarAvailableSurveys = collect();

            foreach ($participants as $participant) {
                $event = $participant->event;
                if (! $event) {
                    continue;
                }

                $canParticipate = $event->userCanParticipateInVoting((int) $user->id);

                foreach ($event->elections()->latest()->get() as $election) {
                    if ($election->candidates->isEmpty()) {
                        continue;
                    }

                    if (! $event->userCanParticipateInVoting((int) $user->id, $election)) {
                        continue;
                    }

                    $hasVoted = $election->votes()
                        ->where('participant_id', $participant->id)
                        ->exists();

                    if (! $hasVoted && $election->status == ElectionStatus::ONGOING) {
                        $sidebarAvailableElections->push([
                            'election' => $election,
                            'event' => $event,
                            'group' => $event->group,
                            'participant' => $participant,
                            'has_voted' => false,
                        ]);
                    } elseif (in_array($election->status, [ElectionStatus::COMPLETED, ElectionStatus::CANCELED, ElectionStatus::ONGOING], true)) {
                        // وکیل نباید انتخابات گذشته را در سایدبار ببیند.
                        if ($election->isFinished() && $participant->is_attorney->isNotEmpty()) {
                            continue;
                        }

                        $sidebarUnavailableElections->push([
                            'election' => $election,
                            'event' => $event,
                            'group' => $event->group,
                            'participant' => $participant,
                            'has_voted' => $hasVoted,
                        ]);
                    }
                }

                if (! $canParticipate) {
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

                    if (! $hasAnswered) {
                        $sidebarAvailableSurveys->push([
                            'survey' => $survey,
                            'event' => $event,
                            'group' => $event->group,
                            'participant' => $participant,
                            'has_answered' => false,
                        ]);
                    }
                }
            }

            $view->with(compact('sidebarAvailableElections', 'sidebarAvailableSurveys', 'sidebarUnavailableElections'));
        });

        View::composer('app.group.*', function ($view) {
            $group = request()->route('group');
            $allocatedNormal = $group?->users()?->sum('group_user.normal_stock_count');
            $allocatedPrefered = $group?->users()?->sum('group_user.prefered_stock_count');

            $remainingNormal = $group?->normal_stock_count - $allocatedNormal;
            $remainingPrefered = $group?->prefered_stock_count - $allocatedPrefered;

            $view->with([
                'allocatedNormal' => $allocatedNormal,
                'allocatedPrefered' => $allocatedPrefered,
                'remainingNormal' => $remainingNormal,
                'remainingPrefered' => $remainingPrefered,
            ]);
        });
    }
}
