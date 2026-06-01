<?php
// #edited
namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class AttendanceUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $group;

    public $presentCount;

    public $totalCount;

    public function __construct(Group $group, int $presentCount, int $totalCount)
    {
        $this->group = $group;
        $this->presentCount = $presentCount;
        $this->totalCount = $totalCount;
    }

    public function broadcastOn()
    {
        return new Channel('group.'.$this->group->id.'.attendance');
    }

    public function broadcastWith()
    {
        return [
            'presentCount' => $this->presentCount,
            'totalCount' => $this->totalCount,
        ];
    }
}
