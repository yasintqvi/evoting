<?php

namespace App\Http\Controllers\Survey;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Models\SurveyResponse;

class SurveyParticipantController extends Controller
{

    public function index()
    {
        return view('app.surveys.my-surveys');
    }

}
