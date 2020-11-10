<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Option;
use App\Models\Variant;
use App\Models\Category;
use App\Facades\Settings;

class TenantFontendDetailsTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_returns_all_details_to_build_the_frontend_app_in_one_call()
    {
        $settings = collect([
            'name' => $this->tenant->name,
            'phone' => $this->tenant->phone,
            'email' => $this->tenant->email,
            'messageOfTheDay' => 'I ate a clock yesterday, it was very time-consuming.',
            'deliveryEnabled' => true,
            'deliveryFee' => 15,
            'primaryColor' => '#da1074',
            'secondaryColor' => '#143642',
            'openingHours' => [
                'monday' => ['10:00-14:00'],
                'tuesday' => ['10:00-14:00'],
                'wednesday' => ['10:00-14:00'],
                'thursday' => ['10:00-14:00', '17:00-23:00'],
                'friday' => ['10:00-14:00', '17:00-23:00'],
                'saturday' => ['08:00-14:00', '17:00-23:00'],
                'sunday' => ['08:00-14:00', '17:00-20:00'],
                'exceptions' => [
                    '01-01' => [],
                    '12-25' => [],
                ],
            ],
        ])->each(function ($item, $key) {
            Settings::set($key, $item);
        });

        $categories = Category::factory()->count(rand(5, 8))->create()->each(function ($category) {
            Item::factory()->available()->count(rand(10, 20))->create([
                'category_id' => $category,
            ])->each(function ($item) {
                Variant::factory()->count(rand(2, 4))->create(['item_id' => $item]);
                Option::factory()->count(rand(1, 5))->create(['item_id' => $item]);
            });
        });

        $response = $this->get(tenant_route($this->tenant->domains()->first()->domain, 'app.frontend'))
            ->assertSuccessful()
            ->assertSee($this->tenant->name)
            ->assertSee($this->tenant->phone)
            ->assertSee($this->tenant->email)
            ->assertSee('settings')
            ->assertSee('isOpen')
            ->assertSee('openingHours')
            ->assertSee('categories')
            ->assertSee('items')
            ->assertSee('variants')
            ->assertSee('options');

        collect([
            'deliveryEnabled' => true,
            'deliveryFee' => 15,
            'primaryColor' => '#da1074',
            'secondaryColor' => '#143642',
        ])->each(function ($item, $key) use ($response) {
            $response
                ->assertSee($item)
                ->assertSee($key);
        });

        $categories->each(function ($category) use ($response) {
            $response
                ->assertSee($category->name)
                ->assertSee($category->description);

            $category->items->each(function ($item) use ($response) {
                $response
                    ->assertSee($item->name)
                    ->assertSee($item->description);

                $item->variants->each(function ($variant) use ($response) {
                    $response
                        ->assertSee($variant->name)
                        ->assertSee($variant->description);
                });

                $item->options->each(function ($option) use ($response) {
                    $response
                        ->assertSee($option->name)
                        ->assertSee($option->description);
                });
            });
        });
    }
}
