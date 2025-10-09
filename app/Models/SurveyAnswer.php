<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyAnswer extends Model
{
    protected $fillable = [
        "response_id",
        "question_id",
        "option_id",
        "answer_text",
    ];
}
