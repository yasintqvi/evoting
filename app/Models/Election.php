<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Election extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'event_id',
        'position_id',
        'owner_id',
        'title',
        'slug',
        'status',
        'type',
        'normal_stock_count',
        'prefered_stock_count',
        'prefered_stock_weight',
        'main_member_count',
        'substitute_member_count',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'همه پرسی', 'subject' => $this->title]))
            ->dontSubmitEmptyLogs();
    }

    public function casts()
    {
        return [
            'type' => ElectionType::class,
            'status' => ElectionStatus::class,
        ];
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function precentParticipants()
    {
        $totalParticipants = $this->participants->count();

        $presentCount = $this->participants->where('is_present', 1)->count();

        return $totalParticipants > 0
            ? ($presentCount / $totalParticipants) * 100
            : 0;
    }
}
