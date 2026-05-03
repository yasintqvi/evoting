<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Election extends Model
{
    use LogsActivity;
    use Sluggable;
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
        'candidate_count',
        'normal_stock_count',
        'prefered_stock_count',
        'prefered_stock_weight',
        'main_member_count',
        'substitute_member_count',
        'starts_at',
        'ends_at',
        'ignore_stock_weight',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'همه پرسی', 'subject' => $this->title]))
            ->dontSubmitEmptyLogs();
    }

    public function casts()
    {
        return [
            'type' => ElectionType::class,
            'status' => ElectionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'ignore_stock_weight' => 'boolean',
        ];
    }

    public function isExpired(): bool
    {
        return $this->ends_at !== null && now()->greaterThan($this->ends_at);
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
        return $this->hasMany(Participant::class, 'event_id', 'event_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(User::class, 'election_blocked_users');
    }

    public function rounds()
    {
        return $this->hasMany(ElectionRound::class);
    }

    public function precentParticipants()
    {
        $totalParticipants = $this->participants->count();

        $presentCount = $this->participants->where('is_present', 1)->count();

        return $totalParticipants > 0
            ? ($presentCount / $totalParticipants) * 100
            : 0;
    }

    public function getAllVotesAttribute(): float|int
    {
        return $this->normal_stock_count + ($this->prefered_stock_count * $this->prefered_stock_weight);
    }
}
