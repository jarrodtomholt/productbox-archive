<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider registerValidationProvider
     */
    public function it_validates_register_fields($field, $value)
    {
        $this->postJson(route('auth.register'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function registerValidationProvider()
    {
        return [
            ['firstname', ''],
            ['firstname', 'asd123'],
            ['lastname', ''],
            ['lastname', 'asd123'],
            ['email', ''],
            ['email', 'not-an-email'],
            ['phone', 'asdfasdfasdfasdf'],
            ['phone', 123456789876],
            ['phone', 12345],
            ['password', ''],
            ['password', 'not a valid password'],
            ['password_confirmation', ''],
        ];
    }

    /** @test */
    public function it_fails_to_register_a_user_if_email_already_exists()
    {
        $registeredUser = User::factory()->create();
        $user = User::factory()->make(['email' => $registeredUser->email])->toArray();

        $this->postJson(route('auth.register'), $user)->assertStatus(422)->assertJsonValidationErrors('email');
    }

    /** @test */
    public function it_fails_to_register_a_user_if_password_confirmation_fails()
    {
        $registeredUser = User::factory()->create();
        $user = User::factory()->make()->toArray();

        $this->postJson(route('auth.register'), array_merge($user, [
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
            'password_confirmation' => 'not-secret',
        ]))->assertStatus(422)->assertJsonValidationErrors('password_confirmation');
    }

    /** @test */
    public function it_registers_a_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->make();

        $this->postJson(route('auth.register'), array_merge($user->toArray(), [
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
            'password_confirmation' => 'ThisIsASuperSecureP$ssw0Rd',
        ]))->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);

        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
        ])->assertSuccessful();
    }
}
