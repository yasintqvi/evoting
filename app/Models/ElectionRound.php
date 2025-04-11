<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ElectionRound extends Model
{
    use LogsActivity;

    protected $fillable = [
        'election_id',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'دور انتخاباتی', 'subject' => $this->election->title]))
            ->dontSubmitEmptyLogs();
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
