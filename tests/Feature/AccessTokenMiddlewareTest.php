<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Tenant;
use App\Models\Address;
use Laravel\Sanctum\Sanctum;

class AccessTokenMiddlewareTest extends TestCase
{
    /** @test */
    public function authenticated_users_can_not_access_management_panel_of_tenant()
    {
        Sanctum::actingAs(User::factory()->create());

        $tenant = Tenant::factory()->active()->create();

        $this->getJson(
            tenant_route($tenant->domains()->first()->domain, 'manage.categories')
        )->assertStatus(401);
    }

    /** @test */
    public function admin_from_another_tenant_cant_manage_other_tenants()
    {
        $tenant = Tenant::factory()->active()->create();
        $tenant2 = Tenant::factory()->active()->create();

        $admin = $tenant2->run(function () {
            return Admin::factory()->create();
        });

        Sanctum::actingAs($admin, [sprintf('manage:%s', $tenant2->id)], 'admin');

        $this->getJson(
            tenant_route($tenant->domains()->first()->domain, 'manage.categories')
        )->assertStatus(401);
    }

    /** @test */
    public function admin_from_a_tenant_can_not_access_an_authenticated_users_profile()
    {
        $tenant = Tenant::factory()->active()->create();

        $admin = $tenant->run(function () {
            return Admin::factory()->create();
        });

        Sanctum::actingAs($admin, [sprintf('manage:%s', $tenant->id)], 'admin');

        $this->postJson(route('auth.address'), Address::factory()->make()->toArray())->assertStatus(401);
    }
}
