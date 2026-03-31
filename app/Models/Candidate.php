<?php

namespace App\Models;

use App\Enums\CandidateType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Candidate extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'election_id',
        'candidate_type',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'کاندیدا', 'subject' => $this->user->full_name]))
            ->dontSubmitEmptyLogs();
    }

    public function casts()
    {
        return [
            'candidate_type' => CandidateType::class,
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

    public function votes(){
        return $this->hasMany(Vote::class);
    }
}
