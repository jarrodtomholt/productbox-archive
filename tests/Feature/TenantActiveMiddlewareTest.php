<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TenantActiveMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_does_not_allow_access_to_tenants_if_inactive()
    {
        $tenant = Tenant::factory()->create();

        $this->get(tenant_route($tenant->domains()->first()->domain, 'app.home'))->assertStatus(404);
    }

    /** @test */
    public function it_allows_access_to_tenants_if_they_are_active()
    {
        $tenant = Tenant::factory()->active()->create();

        $this->get(tenant_route($tenant->domains()->first()->domain, 'app.home'))->assertSuccessful();
    }

    // TODO - add subscription checks
}
