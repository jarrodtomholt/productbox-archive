<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Admin;
use App\Models\Variant;
use Laravel\Sanctum\Sanctum;

class VariantsTest extends TestCase
{
    protected $tenancy = true;

    public function setUp(): void
    {
        parent::setUp();

        $admin = $this->tenant->run(function () {
            return Admin::factory()->create();
        });

        Sanctum::actingAs($admin, [sprintf('manage:%s', $this->tenant->id)], 'admin');
    }

    /**
     * @test
     * @dataProvider variantValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $item = Item::factory()->create();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.variants.store', [
            'item' => $item,
        ]), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function variantValidationProvider()
    {
        return [
            ['name', ''],
            ['price', ''],
            ['price', 'asdf'],
        ];
    }

    /** @test */
    public function it_creates_a_new_item_variant()
    {
        $item = Item::factory()->create();

        $variant = Variant::factory()->make();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.variants.store', [
            'item' => $item,
        ]), $variant->toArray())
        ->assertSuccessful()
        ->assertSee($variant->name);

        $this->assertDatabaseHas('variants', [
            'item_id' => $item->id,
            'name' => $variant->name,
            'price' => intval($variant->price * 100),
        ]);
    }

    /** @test */
    public function it_updates_an_existing_variant()
    {
        $item = Item::factory()->create();

        $variant = Variant::factory()->create(['item_id' => $item, 'name' => 'i am the old vairant name']);

        $updatedVariant = Variant::factory()->make(['name' => 'i am the new vairant name']);

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.variants.update', [
            'item' => $item,
            'variant' => $variant,
        ]), $updatedVariant->toArray())
        ->assertSuccessful()
        ->assertSee($updatedVariant->name)
        ->assertDontSee($variant->name);

        $this->assertDatabaseHas('variants', [
            'name' => $updatedVariant->name,
            'price' => intval($updatedVariant->price * 100),
        ]);

        $this->assertDatabaseMissing('variants', [
            'name' => $variant->name,
        ]);
    }
}
