<?php

namespace App\Models;

use App\Enums\ElectionStatus;
use App\Enums\ElectionType;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
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

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function rounds()
    {
        return $this->hasMany(ElectionRound::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function precentParticipants()
    {
        return $this->participants()->where("is_present", true)->get();
    }
}
