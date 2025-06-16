<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Election extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'company_id',
        'owner_id',
        'supervisor_id',
        'title',
        'slug',
        'status',
        'type',
        'normal_stock_count',
        'prefered_stock_count',
        'prefered_stock_weight',
        'main_member_count',
        'substitute_member_count',
        'incpector_main_member_count',
        'incpector_substitute_member_count',
        'quorum_required',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(ElectionRound::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
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
