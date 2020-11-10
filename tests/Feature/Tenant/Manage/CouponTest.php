<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Coupon;
use Laravel\Sanctum\Sanctum;

class CouponTest extends TestCase
{
    protected $tenancy = true;

    public function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(Admin::factory()->create(), [sprintf('manage:%s', $this->tenant->id)], 'admin');
    }

    /**
     * @test
     * @dataProvider categoryValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.coupons.store'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function categoryValidationProvider()
    {
        return [
            ['code', ''],
            ['value', ''],
            ['value', 'asdf'],
            ['minimum_order', 'asdf'],
            ['expires_at', now()->subDay()],
        ];
    }

    /** @test */
    public function it_creates_a_new_coupon()
    {
        $coupon = Coupon::factory()->make();

        $this->postJson(
            tenant_route($this->tenant->domains()->first()->domain, 'manage.coupons.store'),
            $coupon->toArray(),
        )->assertSuccessful()->assertSee($coupon->name)->assertSee($coupon->description);
    }

    /** @test */
    public function it_updates_an_existing_coupon()
    {
        $coupon = Coupon::factory()->create();

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.coupons.update', [
            'coupon' => $coupon,
        ]), [
            'code' => 'newcouponcode',
            'value' => 20,
            'description' => 'new coupon description',
        ])->assertSuccessful()
        ->assertSee('NEWCOUPONCODE')
        ->assertSee('new coupon description');

        $this->assertDatabaseHas('coupons', [
            'code' => 'NEWCOUPONCODE',
            'value' => 20,
            'description' => 'new coupon description',
        ]);

        $this->assertDatabaseMissing('coupons', [
            'code' => $coupon->code,
            'value' => $coupon->value,
            'description' => $coupon->description,
        ]);
    }

    /** @test */
    public function it_deletes_an_existing_coupon()
    {
        $coupon = Coupon::factory()->create();

        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.coupons.destroy', [
            'coupon' => $coupon,
        ]))->assertSuccessful();

        $this->assertDatabaseMissing('coupons', [
            'code' => $coupon->code,
            'value' => $coupon->value,
            'description' => $coupon->description,
        ]);
    }
}
