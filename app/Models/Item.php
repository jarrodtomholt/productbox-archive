<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Gloudemans\Shoppingcart\CanBeBought;
use Spatie\MediaLibrary\InteractsWithMedia;
use Gloudemans\Shoppingcart\Contracts\Buyable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model implements HasMedia, Buyable
{
    use CanBeBought, HasSlug, HasFactory, InteractsWithMedia;

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

    protected $with = [
        'media',
        'variants',
        'options',
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

    public function getBuyableIdentifier($selections = null)
    {
        return $this->slug;
    }

    public function getBuyableDescription($selections = null)
    {
        return $this->name;
    }

    public function getBuyablePrice($selections = null)
    {
        if (!$selections) {
            return floatval(number_format($this->price / 100, 2));
        }

        $price = $this->price;
        $selections = collect($selections);

        if ($selections->has('variant')) {
            $price = $this->variants->where('slug', $selections->get('variant')['slug'])->first()->price;
        }

        if ($selections->has('options')) {
            $options = collect($selections->get('options'));
            $price += $this->options->whereIn('slug', $options->flatten())->sum('price');
        }

        return floatval(number_format($price / 100, 2));
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
