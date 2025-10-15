<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vote extends Model
{
    use LogsActivity;

    protected $fillable = [
        'election_id',
        'participant_id',
        'candidate_id',
        'vote_count',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'رای', 'subject' => $this->participant->user->full_name]))
            ->dontSubmitEmptyLogs();
    }

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
