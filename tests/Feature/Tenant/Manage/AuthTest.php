<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     * @dataProvider loginValidationProvider
     */
    public function it_validates_username_and_password_fields($field, $value)
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.login'), [
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
        $this->tenant->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        $response = $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.login'), [
            'email' => 'another@randomemail.com',
            'password' => 'secret',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_fails_to_login_with_incorrect_password()
    {
        $admin = Admin::factory()->create(['password' => 'secret']);

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.login'), [
            'email' => $admin->email,
            'password' => 'not-a-secret-password',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_allows_an_admin_to_login()
    {
        $admin = Admin::factory()->create(['password' => 'secret']);

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.login'), [
            'email' => $admin->email,
            'password' => 'secret',
        ])->assertSuccessful()
        ->assertSee('token');
    }

    /** @test */
    public function unauthenticated_users_cant_logout()
    {
        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.logout'))
        ->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_logout()
    {
        $admin = Admin::factory()->create(['password' => 'secret']);

        Sanctum::actingAs($admin, [sprintf('manage:%s', $this->tenant->id)], 'admin');

        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.logout'))
        ->assertSuccessful();
    }
}
