<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     * @dataProvider resetPasswordValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.reset.password'), [
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
    public function it_updates_the_admins_password()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::factory()->create(['password' => 'secret']);
        $token = Password::broker('admins')->createToken($admin);

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.reset.password'), [
            'email' => $admin->email,
            'password' => 'ThisIsASuperSecureP$ssw0Rd',
            'password_confirmation' => 'ThisIsASuperSecureP$ssw0Rd',
            'token' => $token,
        ])->assertSuccessful();

        $this->assertTrue(Hash::check('ThisIsASuperSecureP$ssw0Rd', $admin->fresh()->password));
    }
}
