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
            ->setDescriptionForEvent(function (string $eventName) {
                try {
                    $subject = $this->participant?->user?->full_name ?? 'نامشخص';
                    return __('messages.log_activity', ['event' => __($eventName), 'resource' => 'رای', 'subject' => $subject]);
                } catch (\Exception $e) {
                    return __('messages.log_activity', ['event' => __($eventName), 'resource' => 'رای', 'subject' => 'نامشخص']);
                }
            })
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
