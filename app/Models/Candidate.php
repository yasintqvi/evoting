<?php

namespace App\Models;

use App\Enums\CandidateType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = [
        'user_id',
        'election_id',
        'candidate_type'
    ];

    public function casts()
    {
        return [
            'candidate_type' => CandidateType::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}
