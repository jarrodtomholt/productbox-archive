<?php

namespace Tests\Feature\Tenant\Admin;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Tenant;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    /**
     * @test
     * @dataProvider loginValidationProvider
     */
    public function it_validates_username_and_password_fields($field, $value)
    {
        $tenant = Tenant::factory()->active()->create();

        $this->postJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.login'), [
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
        $tenant = Tenant::factory()->active()->create();

        $tenant->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        $response = $this->postJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.login'), [
            'email' => 'another@randomemail.com',
            'password' => 'secret',
        ])->assertStatus(422);
    }

    /** @test */
    public function it_fails_to_login_with_incorrect_password()
    {
        $tenant = Tenant::factory()->active()->create();

        $admin = $tenant->fresh()->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        $this->postJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.login'), [
            'email' => $admin->email,
            'password' => 'not-a-secret-password',
        ])
        ->assertStatus(422);
    }

    /** @test */
    public function it_allows_an_admin_to_login()
    {
        $tenant = Tenant::factory()->active()->create();

        $admin = $tenant->fresh()->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        $this->postJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.login'), [
            'email' => $admin->email,
            'password' => 'secret',
        ])->assertSuccessful()
        ->assertSee('token');
    }

    /** @test */
    public function unauthenticated_users_cant_logout()
    {
        $tenant = Tenant::factory()->active()->create();
        $this->deleteJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.logout'))
        ->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_logout()
    {
        $tenant = Tenant::factory()->active()->create();

        $admin = $tenant->fresh()->run(function () {
            return Admin::factory()->create(['password' => 'secret']);
        });

        Sanctum::actingAs($admin, [sprintf('manage:%s', $tenant->id)], 'admin');

        $this->deleteJson(tenant_route($tenant->domains()->first()->domain, 'tenant.manage.auth.logout'))
        ->assertSuccessful();
    }

    /** @test */
    public function admin_from_another_tenant_cant_manage_other_tenants()
    {
        $tenant = Tenant::factory()->active()->create();
        $tenant2 = Tenant::factory()->active()->create();

        $admin = $tenant->fresh()->run(function () {
            return Admin::factory()->create();
        });

        Sanctum::actingAs($admin, [sprintf('manage:%s', $tenant->id)], 'admin');

        $this->deleteJson(tenant_route($tenant2->domains()->first()->domain, 'tenant.manage.auth.logout'))
        ->assertStatus(401);
    }
}
