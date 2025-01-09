<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionRound extends Model
{
    protected $fillable = [
        'election_id',
        'start_date',
        'end_date',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
