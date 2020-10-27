<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Manage\ForgotPasswordNotification;

class ForgotPasswordTest extends TestCase
{
    use WithFaker;

    protected $tenancy = true;

    /** @test */
    public function it_validates_an_email_for_a_forgotten_password_request()
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.forgot.password'), [
            'email' => 'invalid.email',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_fails_if_customer_with_the_provided_email_does_not_exist()
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.forgot.password'), [
            'email' => $this->faker->safeEmail,
        ])->assertStatus(422);
    }

    /** @test */
    public function it_sends_a_reset_password_link_if_valid_forgot_password_request()
    {
        Notification::fake();

        $admin = $this->tenant->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.forgot.password'), [
            'email' => $admin->email,
        ])->assertSuccessful();

        Notification::assertSentTo($admin, ForgotPasswordNotification::class);
    }
}
