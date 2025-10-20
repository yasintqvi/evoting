<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        "event_id",
        "title",
        "description",
        "is_anonymous",
        "start_at",
        "end_at",
        "status",
        "created_by"
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

}
