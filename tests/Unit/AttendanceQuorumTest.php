<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\Group;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class AttendanceQuorumTest extends TestCase
{
    public function test_share_quorum_boundary_and_empty_capital(): void
    {
        foreach ([[100, 49, false], [100, 50, true], [100, 51, true], [0, 0, false]] as [$total, $present, $expected]) {
            $event = new Event;
            $event->setRelation('group', new Group(['normal_stock_count' => $total, 'prefered_stock_count' => 0]));
            $event->setRelation('participants', new Collection([
                new Participant(['normal_stock_count' => $present, 'prefered_stock_count' => 0, 'is_present' => true]),
                new Participant(['normal_stock_count' => 30, 'is_present' => false]),
            ]));
            $this->assertSame($expected, $event->attendanceQuorum()['is_official']);
        }
    }

    public function test_attorney_shares_count_once_and_preferred_shares_are_included(): void
    {
        $event = new Event;
        $event->setRelation('group', new Group(['normal_stock_count' => 80, 'prefered_stock_count' => 20]));
        $event->setRelation('participants', new Collection([
            new Participant(['normal_stock_count' => 30, 'prefered_stock_count' => 20, 'is_present' => true]),
            new Participant(['normal_stock_count' => 0, 'prefered_stock_count' => 0, 'delegated_normal_stock_count' => 30, 'attorney_id' => 1, 'is_present' => true]),
        ]));
        $quorum = $event->attendanceQuorum();
        $this->assertSame(50.0, $quorum['present_shares']);
        $this->assertSame(100.0, $quorum['total_shares']);
        $this->assertTrue($quorum['is_official']);
    }
}
