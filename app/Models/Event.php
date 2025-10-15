<?php

namespace App\Models;

use App\Enums\EventStatus;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{
    use LogsActivity;
    use SoftDeletes;
    use Sluggable;

    protected $fillable = [
        'name',
        'title',
        'logo',
        'description',
        'status',
        'quorum_percent',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function casts(): array
    {
        return [
            'status' => EventStatus::class,
        ];
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'رویداد', 'subject' => $this->title]))
            ->dontSubmitEmptyLogs();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

     public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class);
    }

    public function getPresentCountAttribute()
    {
        return $this->attendances()->where('status', 1)->count();
    }

    public function getAbsentCountAttribute()
    {
        return $this->attendances()->where('status', 0)->count();
    }
}
