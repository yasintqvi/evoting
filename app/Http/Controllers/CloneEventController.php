<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Http\Request;

class CloneEventController extends Controller
{
   public function __invoke(Group $group,Event $event)
   {
      $newEvent=$event->cloneWith(['participants','elections.candidates','elections.position',"surveys.questions.options"]);
       $newEvent->surveys()->update(['status' => 0]);
       $newEvent->elections()->update(['status'=>0]);

     return redirect()->back()->with('success',"عملیات با موفقیت انجام شد");
   }
}
