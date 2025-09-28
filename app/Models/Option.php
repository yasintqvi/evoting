<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'survey_options';
    protected $fillable = [
        "question_id",
        "option_text",
        "order"
    ];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

}
