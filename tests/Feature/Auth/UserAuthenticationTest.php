<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider loginValidationProvider
     */
    public function it_validates_email_and_password_fields($field, $value)
    {
        $this->postJson(route('auth.login'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function loginValidationProvider()
    {
        return [
            ['email', ''],
            ['email', 'not-a-valid-email.com'],
            ['password', ''],
        ];
    }

    /** @test */
    public function it_fails_to_login_with_incorrect_email()
    {
        $this->postJson(route('auth.login'), [
            'email' => 'this-is-not@a-registered-email.com',
            'password' => 'incorrect-password',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_fails_to_login_with_incorrect_password()
    {
        $user = User::factory()->create();

        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_allows_users_to_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ])
        ->assertSuccessful()
        ->assertSee('token');
    }

    /** @test */
    public function unauthenticated_users_cant_logout()
    {
        $this->deleteJson(route('auth.logout'))->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_logout()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->deleteJson(route('auth.logout'))->assertSuccessful();
    }
}
