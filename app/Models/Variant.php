<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variant extends Model
{
    use HasSlug, HasFactory;

    protected $fillable = [
        'item_id',
        'name',
        'slug',
        'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = intval($value * 100);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
