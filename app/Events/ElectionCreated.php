<?php

namespace App\Events;

use App\Models\Company;
use App\Models\Election;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElectionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Company $company;

    public Election $election;

    /**
     * Create a new event instance.
     */
    public function __construct(Company $company, Election $election)
    {
        $this->company = $company;

        $this->election = $election;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
