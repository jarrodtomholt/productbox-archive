<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'value',
        'description',
        'minimum_order',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function getExpiredAttribute(): bool
    {
        return $this->expires_at ? $this->expires_at->lt(now()) : false;
    }
}
