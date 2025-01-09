<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'slug',
        'status',
        'type',
        'normal_stock_count',
        'prefered_stock_count',
        'prefered_stock_weight',
        'main_member_count',
        'substitute_member_count'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function rounds()
    {
        return $this->hasMany(Election::class);
    }
}
