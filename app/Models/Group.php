<?php

namespace App\Models;

use App\Enums\GroupStatus;
use App\Enums\GroupType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Group extends Model
{
    use HasFactory;
    use LogsActivity;
    use Sluggable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'normal_stock_count',
        'prefered_stock_count',
        'prefered_stock_weight',
        'owner_id',
        'type',
        'status',
        'logo',
    ];

    protected static $logAttributesToIgnore = ['updated_at'];

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(static::$logAttributes)->dontLogIfAttributesChangedOnly(static::$logAttributesToIgnore)
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn (string $eventName) => __('messages.log_activity', ['event' => __($eventName), 'resource' => 'گروه', 'subject' => $this->title]))
            ->dontSubmitEmptyLogs();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    protected function casts()
    {
        return [
            'status' => GroupStatus::class,
            'type' => GroupType::class,
        ];
    }

    public function getLogoAttribute()
    {
        return is_null($this->attributes['logo']) ? 'assets/img/group.png' : $this->attributes['logo'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_user')->withPivot('normal_stock_count', 'prefered_stock_count');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    public function getTotalPreferedAttribute()
    {
        return $this->prefered_stock_count + $this->normal_stock_count;
    }

    public function getTotalNormalStockAttribute()
    {
        return $this->getRemainingStock('normal_stock_count');
    }

    public function getTotalPreferedStockAttribute()
    {
        return $this->getRemainingStock('prefered_stock_count');
    }

    protected function getRemainingStock($stockType)
    {
        $total = $this->$stockType;
        $assigned = $this->users->sum(fn ($user) => $user->pivot->$stockType);

        return $total - $assigned;
    }

    public function getRemainingWeightedStocksAttribute()
    {
        return $this->total_prefered - $this->assigned_stocks;
    }

    public function userAttendes()
    {
        return $this->belongsToMany(User::class, 'attendances')
            ->withPivot('status')
            ->withTimestamps();
    }
}
