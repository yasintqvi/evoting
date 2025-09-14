<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Log;

class AttendanceChartController extends Controller
{
    public function index(Event $event)
    {
        $event = Event::find(1);

        if (!$event) {
            return response()->json([
                'presentCount' => 0,
                'totalCount' => 0
            ]);
        }

        $totalCount = $event->group->users()->count();
        $presentCount = $event->attendances()->where('status', 1)->count();

        return response()->json([
            'presentCount' => $presentCount,
            'totalCount' => $totalCount
        ]);
    }

}