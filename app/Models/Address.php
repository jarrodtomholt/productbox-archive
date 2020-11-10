<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'address2',
        'city',
        'state',
        'postcode',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
