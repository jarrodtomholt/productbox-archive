<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeviceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $device;

    public function setUp(): void
    {
        parent::setUp();

        $this->device = [
            'token' => (string) Str::uuid(),
            'deviceId' => (string) Str::uuid(),
        ];
    }

    /** @test */
    public function it_captures_device_details()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('auth.device'), [
            'token' => $this->device['token'],
            'deviceId' => $this->device['deviceId'],
            'appVersion' => '0.0.1',
        ])->assertSuccessful();

        $this->assertDatabaseHas('devices', [
            'user_id' => $user->id,
            'token' => $this->device['token'],
            'device_id' => $this->device['deviceId'],
            'app_version' => '0.0.1',
        ]);

        $this->assertEquals(1, auth()->user()->devices()->count());
    }

    /** @test */
    public function it_updates_existing_device_details_rather_than_creating_another()
    {
        $user = User::factory()->create();

        $user->devices()->create(Device::factory()->make([
            'token' => $this->device['token'],
            'device_id' => $this->device['deviceId'],
        ])->toArray());

        Sanctum::actingAs($user);

        $this->postJson(route('auth.device'), [
            'token' => (string) Str::uuid(),
            'deviceId' => $this->device['deviceId'],
        ]);

        $this->assertEquals(1, $user->fresh()->devices()->count());
    }

    /** @test */
    public function it_only_captures_device_details_if_authenticated()
    {
        $this->postJson(route('auth.device'), [
            'token' => $this->device['token'],
            'deviceId' => $this->device['deviceId'],
            'appVersion' => '0.0.1',
        ])->assertStatus(401);

        $this->assertEquals(0, Device::all()->count());
    }
}
