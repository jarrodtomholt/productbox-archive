<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'version',
        'app_version',
        'device_id',
        'ip',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
