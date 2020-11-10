<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use App\Facades\Settings;
use Laravel\Sanctum\Sanctum;
use Spatie\OpeningHours\OpeningHours;

class SettingsTest extends TestCase
{
    protected $tenancy = true;

    public function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(Admin::factory()->create(), [sprintf('manage:%s', $this->tenant->id)], 'admin');
    }

    /** @test */
    public function it_stores_all_settings()
    {
        $settings = [
            'deliveryEnabled' => true,
            'deliveryFee' => 15,
            'primaryColor' => '#da1074',
            'secondaryColor' => '#143642',
        ];

        $response = $this->postJson(
            tenant_route($this->tenant->domains()->first()->domain, 'manage.settings'),
            $settings
        )->assertSuccessful();

        collect($settings)->each(function ($value, $key) use ($response) {
            $response->assertSee($key);

            $this->assertDatabaseHas('settings', [
                'key' => $key,
                'value' => $value,
            ]);
        });
    }

    /** @test */
    public function it_creates_valid_opening_hours()
    {
        $settings = [
            'deliveryEnabled' => false,
            'primaryColor' => '#da1074',
            'secondaryColor' => '#143642',
            'openingHours' => [
                'monday' => ['10:00-14:00'],
                'tuesday' => ['10:00-14:00'],
                'wednesday' => ['10:00-14:00'],
                'thursday' => ['10:00-14:00', '17:00-23:00'],
                'friday' => ['10:00-14:00', '17:00-23:00'],
                'saturday' => ['08:00-14:00', '17:00-23:00'],
                'sunday' => ['08:00-14:00', '17:00-20:00'],
                'exceptions' => [
                    '01-01' => [],
                    '12-25' => [],
                ],
            ],
        ];

        $response = $this->postJson(
            tenant_route($this->tenant->domains()->first()->domain, 'manage.settings'),
            $settings
        )->assertSuccessful();

        $openingHours = OpeningHours::create(Settings::get('openingHours'), 'Australia/Melbourne');
        $this->assertTrue(is_array($openingHours->forWeek()));
    }
}
