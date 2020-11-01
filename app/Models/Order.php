<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'address2',
        'city',
        'state',
        'postcode',
        'delivery_type',
        'items',
        'coupon',
        'subtotal',
        'discount',
        'total',
        'delivery_fee',
        'paid',
        'transaction_id',
        'transaction_source',
        'complete',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'complete' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            $order['number'] = substr((mt_rand(pow(10, 3), pow(10, 4) - 1) * time()), 0, 6);
        });
    }

    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = intval($value * 100);
    }

    public function setDiscountAttribute($value)
    {
        $this->attributes['discount'] = intval($value * 100);
    }

    public function setTotalAttribute($value)
    {
        $this->attributes['total'] = intval($value * 100);
    }

    public function setItemsAttribute($value)
    {
        $this->attributes['items'] = collect($value)->toJson();
    }

    public function setTransactionSourceAttribute($value)
    {
        $this->attributes['transaction_source'] = collect($value)->toJson();
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }
}
