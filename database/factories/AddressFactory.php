<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address' => rand(1, 99) . str_replace('\'', '', $this->faker->streetAddress),
            'address2' => rand(0, 5) > 3 ? str_replace('\'', '', $this->faker->secondaryAddress) : null,
            'city' => str_replace('\'', '', $this->faker->city),
            'state' => $this->faker->randomElement(['VIC', 'NSW', 'QLD', 'SA', 'WA', 'NT', 'ACT', 'TAS']),
            'postcode' => rand(1000, 9999),
        ];
    }
}
