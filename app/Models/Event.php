<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Traits\Cloneable;
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
    use Cloneable;

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

    /**
     * مجموع سهام عادی + ممتاز کاربر در این رویداد (فقط ردیف‌های قابل‌رأی، نه موکل وکالت‌داده).
     */
    public function userParticipatingStock(int $userId): float
    {
        return (float) ($this->participants()
            ->where('user_id', $userId)
            ->whereNull('attorney_id')
            ->selectRaw('COALESCE(SUM(normal_stock_count + prefered_stock_count), 0) as stock_total')
            ->value('stock_total') ?? 0);
    }

    /**
     * در گروه سهامی خاص (و انتخابات سهامی)، بدون سهام نمی‌توان در رأی‌گیری/نظرسنجی شرکت کرد.
     */
    public function userCanParticipateInVoting(int $userId, ?Election $election = null): bool
    {
        $this->loadMissing('group');

        $requiresStock = $this->group?->type === \App\Enums\GroupType::SPECIAL;

        if (
            $election &&
            in_array($election->type, [
                \App\Enums\ElectionType::PRIVATE_JOINT,
                \App\Enums\ElectionType::PRIVATE_JOINT_WITH_88,
            ], true)
        ) {
            $requiresStock = true;
        }

        if (! $requiresStock) {
            return true;
        }

        return $this->userParticipatingStock($userId) > 0;
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
        return $this->attendanceStatsQuery()
            ->where('is_present', 1)
            ->count();
    }

    public function getAbsentCountAttribute()
    {
        return $this->attendanceStatsQuery()
            ->where('is_present', 0)
            ->count();
    }

    /**
     * شناسهٔ وکلایی که فقط به‌جای موکل آمده‌اند و سهام‌دار گروه نیستند.
     * وکیلِ سهام‌دار (مثلاً اکبر/زهرا) در شمارش نفرات می‌ماند؛
     * وکیلِ خارجی بدون سهام گروه (مثلاً حمیدرضا آسوده) شمرده نمی‌شود.
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public function attorneyOnlyParticipantIds()
    {
        $this->loadMissing('group');

        $attorneyIds = $this->participants()
            ->whereNotNull('attorney_id')
            ->pluck('attorney_id')
            ->unique()
            ->filter()
            ->values();

        if ($attorneyIds->isEmpty()) {
            return collect();
        }

        $attorneys = $this->participants()
            ->whereIn('id', $attorneyIds)
            ->get()
            ->keyBy('id');

        $groupStockByUserId = collect();
        if ($this->group && $attorneys->isNotEmpty()) {
            $groupStockByUserId = $this->group->users()
                ->whereIn('users.id', $attorneys->pluck('user_id')->unique())
                ->get()
                ->keyBy('id')
                ->map(fn ($user) => [
                    'normal' => (int) ($user->pivot->normal_stock_count ?? 0),
                    'prefered' => (int) ($user->pivot->prefered_stock_count ?? 0),
                ]);
        }

        $delegatedByAttorney = $this->participants()
            ->whereIn('attorney_id', $attorneyIds)
            ->get()
            ->groupBy('attorney_id');

        return $attorneyIds->filter(function ($attorneyId) use ($attorneys, $delegatedByAttorney, $groupStockByUserId) {
            $attorney = $attorneys->get($attorneyId);
            if (! $attorney) {
                return true;
            }

            // سهام‌دار واقعی گروه: حتی اگر وکیل باشد، در شمارش حاضرین می‌ماند.
            $groupStock = $groupStockByUserId->get($attorney->user_id);
            if ($groupStock && ($groupStock['normal'] > 0 || $groupStock['prefered'] > 0)) {
                return false;
            }

            $principals = $delegatedByAttorney->get($attorneyId, collect());
            $delegatedNormal = (int) $principals->sum('delegated_normal_stock_count');
            $delegatedPrefered = (int) $principals->sum('delegated_prefered_stock_count');

            $ownNormal = (int) $attorney->normal_stock_count - $delegatedNormal;
            $ownPrefered = (int) $attorney->prefered_stock_count - $delegatedPrefered;

            // بدون سهام گروه و بدون سهام شخصی: فقط نمایندهٔ موکل است.
            return $ownNormal <= 0 && $ownPrefered <= 0;
        })->values();
    }

    /**
     * پایهٔ شمارش حضور/غیاب: سهام‌داران و موکلان.
     * وکیلِ بدون سهام شخصی جدا حساب نمی‌شود؛ وکیلِ سهام‌دار شمرده می‌شود.
     */
    protected function attendanceStatsQuery()
    {
        return $this->participants()
            ->visibleForAttendance($this->group)
            ->whereNotIn('id', $this->attorneyOnlyParticipantIds());
    }

    public function scopeFilter($query,$filters){

        if(isset($filters['search'])){
           $query->where('title','like','%'.$filters['search'].'%')->orWhere('name','like','%'.$filters['search'].'%');
        }
        if(isset($filters['status'])){
            if ($filters['status'] == 1) {
                $query->where('status', 0);
            }
            if ($filters['status'] == 2) {
                $query->where('status', 1);
            }
            if ($filters['status'] == 3) {
                $query->where('status', 3);
            }
        }

       return $query;
    }
}
