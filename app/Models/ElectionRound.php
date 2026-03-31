<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ElectionRound extends Model
{
    use LogsActivity;

    protected $table = 'election_rounds';

    protected $fillable = [
        'election_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'دور انتخابات', 'subject' => "دور {$this->id}"]))
            ->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore);
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }
}

