<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model implements HasMedia
{
    use HasSlug, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'available',
    ];

    protected $casts = [
        'price' => 'integer',
        'available' => 'boolean',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = intval($value * 100);
    }

    public function getImageAttribute()
    {
        return optional($this->getFirstMedia('image'))->getUrl();
    }

    public function setImage($image)
    {
        $filename = sprintf('%s.%s', $this->slug, $image->getClientOriginalExtension());

        if ($this->hasMedia('image')) {
            return $this->updateMedia($image, 'image')->usingName($this->name)->usingFileName($filename);
        }
        $this->addMedia($image)->usingName($this->name)->usingFileName($filename)->toMediaCollection('image');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }
}
