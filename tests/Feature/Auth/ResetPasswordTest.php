<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider resetPasswordValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $this->postJson(route('auth.reset.password'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function resetPasswordValidationProvider()
    {
        return [
            ['token', ''],
            ['email', ''],
            ['email', 'not an email'],
            ['password', ''],
            ['password', 'not a valid password'],
        ];
    }

    /** @test */
    public function it_updates_the_users_password()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $this->postJson(route('auth.reset.password'), [
            'email' => $user->email,
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
            'password_confirmation' => 'ThisIsASuperSecureP$ssw0Rd',
            'token' => Password::broker('users')->createToken($user),
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('ThisIsASuperSecureP$ssw0Rd', $user->fresh()->password));
    }
}
