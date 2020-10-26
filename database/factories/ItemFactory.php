<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(4, true),
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence(rand(25, 50)),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'available' => false,
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'available' => true,
            ];
        });
    }
}
