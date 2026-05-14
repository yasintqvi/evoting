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

    /**
     * انتقال همهٔ آرا از یک participant به دیگری؛ در صورت وجود رأی هم‌زمان برای همان انتخابات و کاندیدا، vote_count جمع می‌شود.
     */
    public static function reassignAllFromParticipantTo(int $fromParticipantId, int $toParticipantId): void
    {
        if ($fromParticipantId === $toParticipantId) {
            return;
        }

        $votes = static::query()->where('participant_id', $fromParticipantId)->get();

        foreach ($votes as $vote) {
            $existing = static::query()
                ->where('election_id', $vote->election_id)
                ->where('participant_id', $toParticipantId)
                ->where('candidate_id', $vote->candidate_id)
                ->first();

            if ($existing) {
                $existing->vote_count = (int) $existing->vote_count + (int) $vote->vote_count;
                $existing->save();
                $vote->delete();
            } else {
                $vote->participant_id = $toParticipantId;
                $vote->save();
            }
        }
    }
}
