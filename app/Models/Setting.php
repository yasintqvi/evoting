<?php

namespace App\Models;

use App\Enums\SettingSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'slug',
        'user_id',
        'payload'
    ];

    public function casts()
    {
        return [
            'slug' => SettingSlug::class,
            'payload' => 'object'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
