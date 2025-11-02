<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\Request;

class EventStartController extends Controller
{
    public function __invoke(Group $group,Event $event)
    {
        dd('woring');

    }
}
