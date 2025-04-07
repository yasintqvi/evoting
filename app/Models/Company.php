<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use App\Enums\CompanyType;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
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
        'logo'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    protected function casts()
    {
        return [
            'status' => CompanyStatus::class,
            'type' => CompanyType::class
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_company')->withPivot("normal_stock_count", "prefered_stock_count");
    }

    public function elections(): HasMany
    {
        return $this->hasMany(Election::class);
    }

    public function getTotalPreferedAttribute()
    {
        return ($this->prefered_stock_count * $this->prefered_stock_weight)
         + $this->normal_stock_count;
    }

    public function getAssignedStocksAttribute()
    {
        return $this->users->sum(function($user){
            return $user->pivot->normal_stock_count + 
            ($user->pivot->prefered_stock_count * $this->prefered_stock_weight);
        });
    }

    public function getRemainingWeightedStocksAttribute()
    {
       return $this->total_prefered - $this->assigned_stocks;
    }
    
}
