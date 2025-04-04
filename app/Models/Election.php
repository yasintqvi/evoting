<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use SoftDeletes;

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

    public function casts()
    {
        return [
            'type' => ElectionType::class,
            'status' => ElectionStatus::class,
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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
        return $this->participants()->where("is_present", true)->get();
    }
}
