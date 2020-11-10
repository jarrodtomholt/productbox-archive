<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Device;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

class DeviceTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function it_captures_device_details_if_authenticated()
    {
        $admin = Admin::factory()->create();

        Sanctum::actingAs($admin, [sprintf('manage:%s', $this->tenant->id)], 'admin');

        $token = (string) Str::uuid();
        $deviceId = (string) Str::uuid();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.device'), [
            'token' => $token,
            'deviceId' => $deviceId,
            'appVersion' => '0.0.1',
        ])->assertSuccessful();

        $this->assertDatabaseHas('devices', [
            'user_id' => auth()->user()->id,
            'token' => $token,
            'device_id' => $deviceId,
            'app_version' => '0.0.1',
        ]);

        $this->assertEquals(1, auth()->user()->devices()->count());
    }

    /** @test */
    public function it_updates_existing_device_details_rather_than_creating_another()
    {
        $admin = Admin::factory()->create();
        $admin->devices()->create(Device::factory()->make()->toArray());

        Sanctum::actingAs($admin, [sprintf('manage:%s', $this->tenant->id)], 'admin');

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.device'), [
            'token' => (string) Str::uuid(),
            'deviceId' => $admin->devices()->first()->device_id,
        ]);

        $this->assertEquals(1, $admin->fresh()->devices()->count());
    }

    /** @test */
    public function it_only_captures_device_details_if_authenticated()
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.auth.device'), [
            'token' => (string) Str::uuid(),
            'deviceId' => (string) Str::uuid(),
            'appVersion' => '0.0.1',
        ])->assertStatus(401);

        $this->assertEquals(0, Device::count());
    }
}
