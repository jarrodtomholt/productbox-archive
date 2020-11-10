<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Settings
{
    private $settings;

    public function __construct()
    {
        $this->resolveCache();
    }

    private function resolveCache()
    {
        $this->settings = Cache::remember('settings', now()->addDays(7), function () {
            return collect(Setting::pluck('value', 'key')->toArray());
        });
    }

    public function get($key, $default = null)
    {
        $value = $this->settings->get(Str::camel($key), $default);

        return is_string($value) && is_object(json_decode($value)) ? json_decode($value, true) : $value;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $name => $value) {
                $this->set($name, $value);
            }

            return;
        }

        Setting::updateOrCreate(['key' => Str::camel($key)], ['value' => is_array($value) ? json_encode($value) : $value]);

        Cache::forget('settings');
        $this->resolveCache();
    }
}
