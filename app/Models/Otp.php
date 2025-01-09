<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Otp extends Model
{
    protected $fillable = [
        'code'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
