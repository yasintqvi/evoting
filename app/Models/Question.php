<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'survey_questions';

    protected $fillable = [
        "survey_id",
        "question_text",
        "type",
        "is_required",
        "order"
    ];

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
