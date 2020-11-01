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
        $this->withoutExceptionHandling();

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
    public function it_creates_an_order_when_stripe_charge__is_successful_at_checkout()
    {
        Cart::add(Item::factory()->available()->create());

        $user = [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'deliveryType' => 'pickup',
            'token' => 'tok_au',
        ];

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $user)
        ->assertSuccessful();

        $this->assertDatabaseHas('orders', [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'delivery_type' => 'pickup',
            'subtotal' => intval(Cart::priceTotal() * 100),
            'total' => intval(Cart::total() * 100),
        ]);
    }

    /** @test */
    public function it_calculates_totals_with_coupons_on_checkout()
    {
        Cart::add(Item::factory()->available()->create());

        $coupon = Coupon::factory()->create();

        Cart::setGlobalDiscount($coupon->value);

        session(['coupon' => sprintf('%s - %s', $coupon->code, $coupon->description)]);

        $user = [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'deliveryType' => 'pickup',
            'token' => 'tok_au',
        ];

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $user)
        ->assertSuccessful();

        $this->assertDatabaseHas('orders', [
            'name' => 'Test Customer',
            'email' => 'test.customer@example.dev',
            'phone' => '1234567890',
            'delivery_type' => 'pickup',
            'subtotal' => intval(Cart::priceTotal() * 100),
            'discount' => intval(Cart::discount() * 100),
            'total' => intval(Cart::total() * 100),
            'coupon' => session()->get('coupon'),
        ]);
    }

    // /** @test */
    // public function it_rejects_a_delivery_request_to_checkout_when_delivery_not_enabled()
    // {
    //     Cart::add($item);

    //     $user = array_merge(factory(User::class)->make()->toArray(), ['deliveryType' => 'delivery']);

    //     $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $user)
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors('deliveryType');
    // }

    // /** @test */
    // public function it_validates_address_on_checkout()
    // {
    //     $account = factory(Account::class)->create();

    //     $item = $account->fresh()->tenant()->run(function () {
    //         Settings::set('deliveryEnabled', true);

    //         return factory(Item::class)->create();
    //     });
    //     Cart::add($item);

    //     $user = array_merge(factory(User::class)->make()->toArray(), ['deliveryType' => 'delivery']);

    //     $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $user)
    //         ->assertStatus(422)
    //         ->assertJsonValidationErrors('address')
    //         ->assertJsonValidationErrors('city')
    //         ->assertJsonValidationErrors('state')
    //         ->assertJsonValidationErrors('postcode');
    // }

    // /** @test */
    // public function it_adds_delivery_charge_at_checkout()
    // {
    //     $account = factory(Account::class)->create();

    //     $item = $account->fresh()->tenant()->run(function () {
    //         Settings::set([
    //             'deliveryEnabled' => true,
    //             'deliveryCharge' => 8.5
    //         ]);

    //         return factory(Item::class)->create();
    //     });
    //     Cart::add($item);

    //     $user = [
    //         'name' => 'Test Customer',
    //         'email' => 'test.customer@example.dev',
    //         'phone' => '1234567890',
    //         'address' => '1 fake street',
    //         'city' => 'Somewhere',
    //         'state' => 'VIC',
    //         'postcode' => '1234',
    //         'deliveryType' => 'delivery',
    //         'token' => 'tok_au',
    //     ];

    //     $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'app.checkout'), $user)->assertSuccessful();
    //     $this->assertDatabaseHas('orders', [
    //         'name' => $user['name'],
    //         'email' => $user['email'],
    //         'phone' => $user['phone'],
    //         'delivery_type' => 'delivery',
    //         'address' => $user['address'],
    //         'city' => $user['city'],
    //         'state' => $user['state'],
    //         'postcode' => $user['postcode'],
    //         'subtotal' => intval(Cart::priceTotal() * 100),
    //         'discount' => intval(Cart::discount() * 100),
    //         'total' => intval(Cart::total() * 100),
    //         'delivery_fee' => intval(8.5 * 100),
    //         'coupon' => session()->get('coupon'),
    //     ]);
    // }
}
