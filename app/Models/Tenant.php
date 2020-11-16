<?php

namespace App\Models;

use Laravel\Cashier\Billable;
use Spatie\Sluggable\HasSlug;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Sluggable\SlugOptions;
use Spatie\MediaLibrary\InteractsWithMedia;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase, HasMedia
{
    use Billable, HasSlug, HasFactory, HasDatabase, HasDomains, InteractsWithMedia;

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = [
        'logo',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'abn',
            'email',
            'phone',
            'timezone',
            'active',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getLogoAttribute()
    {
        return optional($this->getFirstMedia('logo'))->getUrl();
    }

    public function setLogo($image)
    {
        $filename = sprintf('%s.%s', $this->slug, $image->getClientOriginalExtension());

        if ($this->hasMedia('logo')) {
            return $this->updateMedia($image, 'logo')->usingName($this->name)->usingFileName($filename);
        }
        $this->addMedia($image)->usingName($this->name)->usingFileName($filename)->toMediaCollection('logo');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }
}
