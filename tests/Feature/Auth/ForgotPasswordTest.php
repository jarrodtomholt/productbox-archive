<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\Auth\ResetPasswordNotification;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_validates_an_email_for_a_forgotten_password_request()
    {
        $this->postJson(route('auth.forgot.password'), [
            'email' => 'invalid.email',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_fails_if_customer_with_the_provided_email_does_not_exist()
    {
        $this->postJson(route('auth.forgot.password'), [
            'email' => $this->faker->safeEmail,
        ])->assertStatus(422);
    }

    /** @test */
    public function it_sends_a_reset_password_link_if_valid_forgot_password_request()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('auth.forgot.password'), [
            'email' => $user->email,
        ])->assertSuccessful();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
