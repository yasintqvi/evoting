<?php

namespace App\Models;

use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Participant extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'election_id',
        'user_id',
        'attorney_id',
        'event_id',
        'normal_stock_count',
        'prefered_stock_count',
        'is_present',
        'group_id',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'گروه کننده', 'subject' => $this->user->full_name]))
            ->dontSubmitEmptyLogs();
    }

    public function casts(): array
    {
        return [
            'is_present' => 'boolean',
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

    // public function getTotalStockAttribute()
    // {
    //     return $this->election->type == ElectionType::PUBLIC_JOINT ? 1 : (int) ($this->normal_stock_count + ($this->prefered_stock_count * $this->election->prefered_stock_weight));
    // }

    public function getTotalStockAttribute()
    {
        $event = $this->event; // از رابطه event
        $election = $event?->elections()?->first(); // یا هر منطق مناسب برای پیدا کردن election

        if (!$election) {
            return 0;
        }

        return $election->type == ElectionType::PUBLIC_JOINT
            ? 1
            : (int) ($this->normal_stock_count + ($this->prefered_stock_count * $election->prefered_stock_weight));
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function attorney(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'attorney_id');
    }

    public function is_attorney()
    {
        return $this->hasMany(Participant::class, 'attorney_id');
    }
}
