<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Device::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'token' => (string) Str::uuid(),
            'device_id' => (string) Str::uuid(),
            'app_version' => '0.0.1',
            'ip' => '123.456.789.000',
        ];
    }
}
