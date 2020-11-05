<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Coupon;
use Gloudemans\Shoppingcart\Facades\Cart;

class CheckoutTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_must_have_items_in_the_cart_to_proceed_to_checkout()
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'))->assertStatus(403);
    }

    /**
     * @test
     * @dataProvider checkoutValidationProvider
     */
    public function it_validates_required_checkout_fields($field, $value)
    {
        Cart::add(Item::factory()->available()->create());

        $response = $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function checkoutValidationProvider()
    {
        return [
            ['name', ''],
            ['email', ''],
            ['email', 'not an email'],
            ['phone', ''],
            ['phone', 'asdfasdfasdfasdf'],
        ];
    }

    /**
     * @test
     * @dataProvider stripeInvalidTokensProvider
     */
    public function it_catches_stripe_exceptions_on_checkout($token)
    {
        Cart::add(Item::factory()->available()->create());

        $customer = [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'deliveryType' => 'pickup',
            'token' => $token,
        ];

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $customer)
        ->assertStatus(422);
    }

    public function stripeInvalidTokensProvider()
    {
        return [
            ['completely_fake_token'],
            ['tok_chargeDeclined'],
            ['tok_chargeDeclinedInsufficientFunds'],
            ['tok_chargeDeclinedFraudulent'],
            ['tok_chargeDeclinedProcessingError'],
        ];
    }

    /** @test */
    public function it_creates_an_order_when_stripe_charge_is_successful_at_checkout()
    {
        Cart::add(Item::factory()->available()->create());

        $customer = [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'token' => 'tok_au',
        ];

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $customer)
        ->assertSuccessful();

        $this->assertDatabaseHas('orders', [
            'name' => $customer['name'],
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'subtotal' => intval(Cart::priceTotal() * 100),
            'coupon' => null,
            'total' => intval(Cart::total() * 100),
        ]);
    }

    /** @test */
    public function it_calculates_totals_with_coupons_on_checkout()
    {
        Cart::add(Item::factory()->available()->create());

        $coupon = Coupon::factory()->create();

        Cart::setGlobalDiscount($coupon->value);

        // this sets the coupon in session per CouponController::class
        session(['coupon' => sprintf('%s - %s', $coupon->code, $coupon->description)]);

        $customer = [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'token' => 'tok_au',
        ];

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $customer)
        ->assertSuccessful();

        $this->assertDatabaseHas('orders', [
            'name' => $customer['name'],
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'subtotal' => intval(Cart::priceTotal() * 100),
            'discount' => intval(Cart::discount() * 100),
            'total' => intval(Cart::total() * 100),
            'coupon' => session()->get('coupon'),
        ]);
    }
}
