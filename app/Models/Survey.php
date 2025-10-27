<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use Sluggable;
    protected $fillable = [
        "event_id",
        "title",
        "description",
        "is_anonymous",
        "start_at",
        "end_at",
        "status",
        "created_by",
    ];

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

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        if (isset($filters['status'])) {
            if ($filters['status'] == 1) {
                $query->where('status', 1);
            }
            if ($filters['status'] == 2) {
                $query->where('status', 0);
            }
        }
        if (isset($filters['is_anonymous'])) {
            if ($filters['is_anonymous'] == 1) {
                $query->where('is_anonymous', 1);
            }
            if ($filters['is_anonymous'] == 2) {
                $query->where('is_anonymous', 0);
            }
        }
    }

}
