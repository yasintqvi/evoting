<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'title',
        'logo',
        'description',
        'status',
        'quorum_percent'
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;


    public function casts(): array
    {
        return [
            'status' => EventStatus::class
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'رویداد', 'subject' => $this->title]))
            ->dontSubmitEmptyLogs();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }
}
