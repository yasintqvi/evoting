<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'to',
        'content',
        'handler',
        'pattern',
        'meta',
    ];

    public function casts()
    {
        return [
            'meta' => 'object',
        ];
    }

    public const UPDATED_AT = null;

    public function scopeHasValidationCode(Builder $query, string $phone)
    {
        $expire_time = config('auth.constants.new_sms_expire_minutes');

        return $query->where('to', $phone)
            ->where('created_at', '>=', now()->subMinutes($expire_time)->toDateTimeString())
            ->orderBy('created_at', 'desc');
    }

    public function scopeCheckValidationCode(Builder $query, string $phone, string $otp_code)
    {
        $expire_time = config('auth.constants.new_sms_expire_minutes');

        return $query->where('to', $phone)->where('content', '=', $otp_code)
            ->where('created_at', '>=', now()->subMinutes($expire_time)->toDateTimeString())
            ->orderBy('created_at', 'desc');
    }
}
