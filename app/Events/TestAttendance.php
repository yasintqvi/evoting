<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestAttendance
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $event_id;

    public function __construct($event_id, $message)
    {
        $this->event_id = $event_id;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('test.event.' . $this->event_id),
        ];
    }

    public function broadcastAs()
    {
        return 'AttendanceTest';
    }
}
