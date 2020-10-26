<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Option::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(4, true),
            'item_id' => Item::factory(),
            'price' => $this->faker->randomFloat(2, 5, 100),
        ];
    }
}
