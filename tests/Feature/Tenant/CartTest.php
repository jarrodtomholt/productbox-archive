<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Option;
use App\Models\Variant;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_returns_an_empty_cart()
    {
        $this->get(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'))
            ->assertSuccessful()
            ->assertJsonFragment(['total' => 0]);
    }

    /** @test */
    public function it_adds_an_item_to_the_cart_and_returns_the_cart_contents()
    {
        $item = Item::factory()->available()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ])
        ->assertSuccessful()
        ->assertSee($item->slug)
        ->assertSee($item->name);

        $this->assertEquals(Cart::subtotal(), number_format($item->price / 100, 2));
    }

    /** @test */
    public function it_fails_to_add_an_item_that_is_unavailable()
    {
        $item = Item::factory()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ])
        ->assertStatus(404);

        $this->assertEquals(Cart::subtotal(), 0);
    }

    /** @test */
    public function it_increments_cart_quantity_and_returns_the_cart_contents()
    {
        $item = Item::factory()->available()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ])->assertSuccessful();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ])
        ->assertSuccessful()
        ->assertSee($item->slug)
        ->assertSee($item->name);

        $this->assertEquals(Cart::subtotal(), number_format($item->price * 2 / 100, 2));
    }

    /** @test */
    public function it_adds_multiple_items_to_the_cart_and_returns_the_cart_contents()
    {
        $howMany = rand(2, 8);

        Item::factory()->count($howMany)->available()->create()->each(function ($item) {
            $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
                'item' => $item->slug,
            ])
            ->assertSuccessful()
            ->assertSee($item->slug)
            ->assertSee($item->name);
        });

        $this->get(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'))->assertSuccessful();
    }

    /** @test */
    public function it_updates_items_with_rowid_and_returns_cart_content()
    {
        $item = Item::factory()->available()->create();

        $response = $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ]);

        $rowId = collect(json_decode($response->getContent(), true)['content'])->pluck('rowId')->first();

        $this->put(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'rowId' => $rowId,
            'quantity' => 100,
        ])->assertSuccessful();

        $this->assertEquals(Cart::subtotal(), number_format(($item->price / 100) * 100, 2));
    }

    /** @test */
    public function it_deletes_items_with_rowid_and_returns_cart_content()
    {
        $item = Item::factory()->available()->create();

        $response = $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ]);

        $rowId = collect(json_decode($response->getContent(), true)['content'])->pluck('rowId')->first();

        $this->delete(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'rowId' => $rowId,
        ])->assertSuccessful()
        ->assertJsonFragment(['total' => 0]);
    }

    /** @test */
    public function it_clears_the_cart_and_returns_cart_content()
    {
        Item::factory()->count(rand(1, 10))->available()->create()->each(function ($item) {
            $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
                'item' => $item->slug,
            ]);
        });

        $this->delete(tenant_route($this->tenant->domains()->first()->domain, 'app.cart.clear'))
        ->assertSuccessful()
        ->assertJsonFragment(['total' => 0]);
    }

    /** @test */
    public function it_adds_requested_variant_and_calculates_price()
    {
        $item = Item::factory()->available()->create();

        $variant = Variant::factory()->create([
            'item_id' => $item
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variant,
            ],
        ])->assertSuccessful();

        $this->assertEquals(Cart::subtotal(), number_format($variant->price / 100, 2));
    }

    /** @test */
    public function it_adds_requested_option_and_calculates_price()
    {
        $item = Item::factory()->available()->create();

        $option = Option::factory()->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'options' => $option,
            ],
        ])->assertSuccessful();

        $expectedPrice = ($item->price + $option->price) / 100;

        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_multiple_options_and_calculates_price()
    {
        $item = Item::factory()->available()->create();

        $options = Option::factory()->count(rand(2, 8))->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'options' => $options->toArray(),
            ],
        ])->assertSuccessful();

        $expectedPrice = ($item->price + $options->sum('price')) / 100;
        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_variant_and_single_option_and_calculates_price()
    {
        $item = Item::factory()->available()->create();

        $variant = Variant::factory()->create([
            'item_id' => $item,
        ]);

        $option = Option::factory()->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variant,
                'options' => $option,
            ],
        ])->assertSuccessful();

        $expectedPrice = ($variant->price + $option->price) / 100;

        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_variant_and_multiple_options_and_calculates_price()
    {
        $item = Item::factory()->available()->create();

        $variant = Variant::factory()->create([
            'item_id' => $item,
        ]);

        $options = Option::factory()->count(rand(2, 8))->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variant,
                'options' => $options->toArray(),
            ],
        ])->assertSuccessful();

        $expectedPrice = ($variant->price + $options->sum('price')) / 100;
        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_variant_and_non_variant_as_separate_items()
    {
        $item = Item::factory()->available()->create();

        $variant = Variant::factory()->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variant,
            ],
        ])->assertSuccessful();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ])->assertSuccessful();

        $expectedPrice = ($item->price + $variant->price) / 100;

        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_different_variants_as_separate_items()
    {
        $item = Item::factory()->available()->create();

        $variants = Variant::factory()->count(2)->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variants->first(),
            ],
        ])->assertSuccessful();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'variant' => $variants->last(),
            ],
        ])->assertSuccessful();

        $expectedPrice = ($variants->first()->price + $variants->last()->price) / 100;

        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_adds_different_options_as_separate_items()
    {
        $item = Item::factory()->available()->create();

        $options = Option::factory()->count(2)->create([
            'item_id' => $item,
        ]);

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'options' => $options->first(),
            ],
        ])->assertSuccessful();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
            'selections' => [
                'options' => $options->last(),
            ],
        ])->assertSuccessful();

        $expectedPrice = (($item->price * 2) + $options->sum('price')) / 100;
        $this->assertEquals(Cart::subtotal(), number_format($expectedPrice, 2));
    }

    /** @test */
    public function it_fails_to_add_an_item_to_the_cart_that_does_not_exist()
    {
        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => 'this-item-does-not-exist',
        ])->assertStatus(404);
    }

    /** @test */
    public function it_fails_to_update_a_cart_row_where_rowid_does_not_exist()
    {
        $this->put(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'rowId' => 'this-is-completely-fake',
            'quantity' => 100,
        ])->assertStatus(500);
    }

    /** @test */
    public function it_fails_to_delete_a_cart_row_where_rowid_does_not_exist()
    {
        $this->delete(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'rowId' => 'this-is-completely-fake',
        ])->assertStatus(500);
    }
}
