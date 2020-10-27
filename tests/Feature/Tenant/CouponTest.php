<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Coupon;
use Gloudemans\Shoppingcart\Facades\Cart;

class CouponTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_adds_a_valid_coupon()
    {
        $this->withoutExceptionHandling();

        $coupon = Coupon::factory()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => $coupon->code,
        ])->assertSuccessful()
        ->assertSessionHas('coupon', sprintf('%s - %s', $coupon->code, $coupon->description));
    }

    /** @test */
    public function it_fails_when_coupon_has_expired()
    {
        $coupon = Coupon::factory()->expired()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => $coupon->code,
        ])->assertStatus(404);
    }

    /** @test */
    public function it_fails_when_not_actually_a_coupon()
    {
        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => 'totally-not-a-coupon',
        ])->assertStatus(404);
    }

    /** @test */
    public function it_calculates_cart_discount_bases_on_a_single_item()
    {
        $item = Item::factory()->available()->create();

        $response = $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
            'item' => $item->slug,
        ]);

        $coupon = Coupon::factory()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => $coupon->code,
        ]);

        $expectedDiscount = ($item->price * ($coupon->value / 100)) / 100;

        $this->assertEquals(Cart::discount(), number_format($expectedDiscount, 2));
    }

    /** @test */
    public function it_calculates_cart_discount_bases_on_multiple_items()
    {
        $items = Item::factory()->available()->count(rand(2, 8))->create(['price' => 10]);

        $items->each(function ($item) {
            $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
                'item' => $item->slug,
            ]);
        });

        $coupon = Coupon::factory()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => $coupon->code,
        ]);

        $expectedDiscount = ($items->sum('price') * ($coupon->value / 100)) / 100;

        $this->assertEquals(Cart::discount(), number_format($expectedDiscount, 2));
    }

    /** @test */
    public function a_coupon_can_be_removed()
    {
        $items = Item::factory()->available()->count(rand(2, 8))->create();

        $items->each(function ($item) {
            $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.cart'), [
                'item' => $item->slug,
            ]);
        });

        $coupon = Coupon::factory()->create();

        $this->post(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'), [
            'code' => $coupon->code,
        ]);

        $this->delete(tenant_route($this->tenant->domains()->first()->domain, 'app.coupon'));

        $this->assertEquals(Cart::discount(), 0);
    }
}
