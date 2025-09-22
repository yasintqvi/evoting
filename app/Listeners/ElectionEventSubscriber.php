<?php

namespace App\Listeners;

use App\DTOs\Participant\CreateParticipantDto;
use App\Events\ElectionCreated;
use App\Services\ParticipantService;
use Illuminate\Contracts\Events\Dispatcher;

class ElectionEventSubscriber
{
    private ParticipantService $participantService;

    public function __construct(ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    /**
     * Handle user login events.
     */
    public function handleElectionCreated(ElectionCreated $event): void
    {
        $groupShareholders = $event->group->users;

        foreach ($groupShareholders as $shareholder) {

            $this->participantService->create(new CreateParticipantDto(
                $event->election->id,
                $shareholder->id,
                $shareholder->pivot->normal_stock_count,
                $shareholder->pivot->prefered_stock_count,
                (bool) ! $event->election->quorum_required
            ));
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            ElectionCreated::class,
            [self::class, 'handleElectionCreated']
        );
    }
}
