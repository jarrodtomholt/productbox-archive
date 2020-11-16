<?php

namespace Tests\Feature\Onboarding;

use Stripe\OAuth;
use Tests\TestCase;
use App\Models\Tenant;
use Illuminate\Support\Str;

class StripeConnectTest extends TestCase
{
    /** @test */
    public function it_creates_a_valid_redirect_for_stripe_connect_at_setup()
    {
        $tenant = Tenant::factory()->create();
        $response = $this->get(route('stripe.attempt', $tenant))->assertStatus(302);

        $redirect = parse_url($response->headers->get('Location'));
        parse_str($redirect['query'], $params);

        $this->assertTrue(array_key_exists('state', $params));
        $this->assertTrue(cache()->has($params['state']));
        $this->assertTrue($tenant->is(cache()->get($params['state'])));
    }

    /** @test */
    public function it_stores_a_stripe_connect_id_via_oauth_redirect()
    {
        $mockedStripeOAuthUserId = 'this-is-a-mocked-stripe-id';

        $tenant = Tenant::factory()->create();

        $uuid = Str::uuid()->toString();
        cache()->set($uuid, $tenant, now()->addMinutes(5));

        $mock = $this->mock(OAuth::class, function ($mock) use ($mockedStripeOAuthUserId) {
            $mock->shouldReceive('token')->once()->andReturn(
                json_decode(json_encode(['stripe_user_id' => $mockedStripeOAuthUserId])),
                true
            );
        });

        app()->instance(OAuth::class, $mock);

        $this->get(route('stripe.connect', [
            'state' => $uuid,
            'code' => 'this-is-fake',
        ]));

        $this->assertTrue($tenant->stripe_connect_id === $mockedStripeOAuthUserId);
    }
}
