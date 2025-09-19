<?php

namespace App\Events;

use App\Models\Election;
use App\Models\Group;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElectionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Group $group;

    public Election $election;

    /**
     * Create a new event instance.
     */
    public function __construct(Group $group, Election $election)
    {
        $this->group = $group;

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
