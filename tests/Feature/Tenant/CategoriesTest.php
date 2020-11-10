<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;

class CategoriesTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_only_returns_items_that_are_available()
    {
        $category = Category::factory()->create();

        $availableItems = Item::factory()->available()->count(rand(2, 5))->create([
            'category_id' => $category,
        ]);

        $unavailableItems = Item::factory()->count(rand(2, 5))->create([
            'category_id' => $category,
        ]);

        $response = $this->get(tenant_route($this->tenant->domains()->first()->domain, 'app.categories'))
            ->assertSuccessful();

        $availableItems->each(function ($item) use ($response) {
            $response->assertSee($item->name)->assertSee($item->description);
        });

        $unavailableItems->each(function ($item) use ($response) {
            $response->assertDontSee($item->name)->assertDontSee($item->description);
        });
    }
}
