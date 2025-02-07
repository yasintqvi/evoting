<?php

namespace App\Models;

use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $fillable = [
        'election_id',
        'user_id',
        'normal_stock_count',
        'prefered_stock_count',
        'is_present'
    ];

    public function casts(): array
    {
        return [
            'is_present' => 'boolean'
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

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->election->type == ElectionType::PUBLIC_JOINT ?  1 : (int) ($this->normal_stock_count + ($this->prefered_stock_count * $this->election->prefered_stock_weight));
    }
}
