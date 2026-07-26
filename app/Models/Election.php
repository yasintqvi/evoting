<?php

namespace App\Models;

use App\Enums\CandidateType;
use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
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
        'event_id',
        'parent_election_id',
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
                'includeTrashed' => true,
            ],
        ];
    }

    /**
     * اسلاگ فقط باید در همان رویداد یکتا باشد (نه در کل جدول).
     */
    public function scopeWithUniqueSlugConstraints(Builder $query, Model $model, string $attribute, array $config, string $slug): Builder
    {
        if ($model instanceof self && $model->event_id) {
            return $query->where('event_id', $model->event_id);
        }

        return $query;
    }

    /**
     * @param  mixed  $value
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?: $this->getRouteKeyName();
        $event = request()->route('event');
        if ($event instanceof Event) {
            return static::query()
                ->where($field, $value)
                ->where('event_id', $event->getKey())
                ->firstOrFail();
        }

        return static::query()
            ->where($field, $value)
            ->firstOrFail();
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

    /**
     * انتخابات از نظر وضعیت ذخیره‌شده تمام شده یا لغو شده است (لیست گذشته / حذف از لیست شخصی).
     */
    public function isFinished(): bool
    {
        $status = $this->status instanceof ElectionStatus
            ? $this->status
            : ElectionStatus::tryFrom((string) $this->getRawOriginal('status'));

        return $status === ElectionStatus::COMPLETED || $status === ElectionStatus::CANCELED;
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

    public function parentElection(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_election_id');
    }

    public function runoffElections(): HasMany
    {
        return $this->hasMany(self::class, 'parent_election_id');
    }

    public function isRunoff(): bool
    {
        return $this->parent_election_id !== null;
    }

    public function precentParticipants()
    {
        $hiddenUserIds = $this->group->managerOnlyUserIds();

        $participants = $this->participants->whereNotIn('user_id', $hiddenUserIds);

        $totalParticipants = $participants->count();

        $presentCount = $participants->where('is_present', 1)->count();

        return $totalParticipants > 0
            ? ($presentCount / $totalParticipants) * 100
            : 0;
    }

    /**
     * سقف کل آرای قابل ثبت در این انتخابات، بر اساس شرکت‌کنندگان حاضر و نوع انتخابات.
     *
     * - تعاونی: تعداد رأی‌دهندگان (با وکالت)
     * - سهامی بدون ماده ۸۸: مجموع سهام حاضرین × حداکثر تعداد انتخاب مجاز
     *   (چون با انتخاب هر کاندیدا، کل سهم دوباره ثبت می‌شود)
     * - ماده ۸۸: مجموع سهام حاضرین × تعداد اعضای اصلی (سقف استخر رأی)
     */
    public function getAllVotesAttribute(): float|int
    {
        $this->loadMissing(['event.participants', 'group', 'candidates']);

        $hiddenUserIds = $this->group?->managerOnlyUserIds() ?? [];
        $participants = $this->event?->participants ?? collect();

        if ($hiddenUserIds !== []) {
            $participants = $participants->whereNotIn('user_id', $hiddenUserIds);
        }

        if ($this->type === ElectionType::PUBLIC_JOINT) {
            $voters = $participants
                ->where('is_present', true)
                ->whereNull('attorney_id');

            $total = 0;
            foreach ($voters as $voter) {
                $representedCount = $participants->where('attorney_id', $voter->id)->count();
                $total += 1 + $representedCount;
            }

            return $total;
        }

        // فقط کسانی که می‌توانند رأی بدهند (سهام نزد خودشان/وکالت‌گرفته‌ها)
        $presentVoters = $participants
            ->where('is_present', true)
            ->whereNull('attorney_id');

        $totalStock = 0.0;
        foreach ($presentVoters as $participant) {
            $totalStock += $this->participantVoteUnits($participant);
        }

        $seatMultiplier = $this->maxVoteSeatMultiplier();
        $total = $totalStock * $seatMultiplier;

        return $total == (int) $total ? (int) $total : $total;
    }

    /**
     * ضریب سقف رأی نسبت به سهام:
     * ماده ۸۸ و سهامی معمولی = تعداد کرسی قابل‌انتخاب (اعضای اصلی، یا تعداد کاندیدا).
     */
    protected function maxVoteSeatMultiplier(): int
    {
        $mainSeats = max(0, (int) $this->main_member_count);
        $directorCount = $this->candidates
            ->where('candidate_type', CandidateType::DIRECTOR)
            ->count();

        if ($this->type === ElectionType::PRIVATE_JOINT_WITH_88) {
            return $mainSeats;
        }

        if ($this->type === ElectionType::PRIVATE_JOINT) {
            if ($mainSeats <= 0) {
                return max(0, $directorCount);
            }

            return $directorCount > 0 ? min($mainSeats, $directorCount) : $mainSeats;
        }

        return 1;
    }

    protected function participantVoteUnits(Participant $participant): float
    {
        if ($this->ignore_stock_weight) {
            return (float) $participant->normal_stock_count + (float) $participant->prefered_stock_count;
        }

        return (float) $participant->normal_stock_count
            + ((float) $participant->prefered_stock_count * (float) $this->prefered_stock_weight);
    }

    /**
     * متن قابل کپی برای ایجاد همه‌پرسی مشابه (بدون نام افراد؛ فقط شناسهٔ کاربران مسدود).
     */
    public function templateBlockForCopy(): string
    {
        $blockedIds = $this->blockedUsers()->pluck('users.id')->filter()->implode(',');

        $safeTitle = preg_replace('/\s+/', ' ', trim(str_replace(["\r", "\n"], ' ', $this->title)));

        $lines = [
            '[ELECTION_TEMPLATE]',
            'version=1',
            'title='.$safeTitle,
            'type='.$this->type->value,
            'position_id='.$this->position_id,
            'main_member_count='.$this->main_member_count,
            'substitute_member_count='.$this->substitute_member_count,
            'blocked_user_ids='.$blockedIds,
            '[/ELECTION_TEMPLATE]',
            '',
            '---',
            'این بلوک را در صفحهٔ «ایجاد همه‌پرسی» در قسمت «چسباندن قالب» بچسبانید. نام افراد در متن نیست؛ فقط شناسهٔ کاربران مسدود از رأی ذکر شده است.',
        ];

        return implode("\n", $lines);
    }

    /**
     * عنوان پیشنهادی برای کپی/همه‌پرسی جدید تا با عنوانهای موجود در رویداد تداخل نداشته باشد.
     */
    public static function suggestDuplicateTitle(Event $event, string $baseTitle): string
    {
        $base = trim($baseTitle);
        if ($base === '') {
            $base = 'همه‌پرسی';
        }

        $candidate = $base.' (کپی)';
        $n = 2;

        while ($event->elections()->where('title', $candidate)->exists()) {
            $candidate = $base.' (کپی '.$n.')';
            $n++;
        }

        return $candidate;
    }
}
