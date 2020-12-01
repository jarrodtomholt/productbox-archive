<?php

namespace Tests\Feature\Onboarding;

use Tests\TestCase;
use App\Models\Tenant;
use Stripe\PaymentMethod;
use Illuminate\Support\Str;
use Stripe\Exception\InvalidRequestException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider onboardingValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $response = $this->postJson(route('signup'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function onboardingValidationProvider()
    {
        return [
            ['name', ''],
            ['phone', ''],
            ['phone', 'asdfasdfasdfasdf'],
            ['phone', 123456789876],
            ['phone', 12345],
            ['email', ''],
            ['email', 'not an email'],
            ['stripeToken', ''],
        ];
    }

    /** @test */
    public function it_validates_payment_method()
    {
        $tenant = array_merge(Tenant::factory()->make()->toArray(), [
            'stripeToken' => Str::slug(Str::random()),
        ]);

        $this->withoutExceptionHandling();
        $this->expectException(InvalidRequestException::class);
        $this->postJson(route('signup'), $tenant);
    }

    /** @test */
    public function it_creates_a_stripe_customer_and_subscription()
    {
        // simulate payment method from stripe elements
        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => now()->addMonth()->month,
                'exp_year' => now()->addYear()->year,
                'cvc' => '314',
            ]
        ]);

        $postData = array_merge(Tenant::factory()->make()->toArray(), [
            'stripeToken' => $paymentMethod->id,
        ]);

        $this->postJson(route('signup'), $postData);

        $this->assertTrue(Tenant::first()->subscribed('default'));
    }

    /** @test */
    public function it_creates_a_subdomain_when_a_signup_is_successful()
    {
        // simulate payment method from stripe elements
        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => now()->addMonth()->month,
                'exp_year' => now()->addYear()->year,
                'cvc' => '314',
            ],
        ]);

        $postData = array_merge(Tenant::factory()->make()->toArray(), [
            'stripeToken' => $paymentMethod->id,
        ]);

        $this->postJson(route('signup'), $postData);

        $tenant = Tenant::first();

        $this->assertSame(
            sprintf("%s.%s", $tenant->slug, config('app.domain')),
            $tenant->domains()->first()->domain
        );
    }
}
