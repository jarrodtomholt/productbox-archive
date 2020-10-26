<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Admin;
use App\Models\Option;
use Laravel\Sanctum\Sanctum;

class OptionsTest extends TestCase
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
     * @dataProvider OptionValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $item = Item::factory()->create();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.options.store', [
            'item' => $item,
        ]), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function OptionValidationProvider()
    {
        return [
            ['name', ''],
            ['price', ''],
            ['price', 'asdf'],
        ];
    }

    /** @test */
    public function it_creates_a_new_item_option()
    {
        $item = Item::factory()->create();

        $option = Option::factory()->make();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.options.store', [
            'item' => $item,
        ]), $option->toArray())
        ->assertSuccessful()
        ->assertSee($option->name);

        $this->assertDatabaseHas('options', [
            'item_id' => $item->id,
            'name' => $option->name,
            'price' => intval($option->price * 100),
        ]);
    }

    /** @test */
    public function it_updates_an_existing_option()
    {
        $item = Item::factory()->create();

        $option = Option::factory()->create(['item_id' => $item, 'name' => 'i am the old option name']);

        $updatedOption = Option::factory()->make(['name' => 'i am the new option name']);

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'manage.options.update', [
            'item' => $item,
            'option' => $option,
        ]), $updatedOption->toArray())
        ->assertSuccessful()
        ->assertSee($updatedOption->name)
        ->assertDontSee($option->name);

        $this->assertDatabaseHas('options', [
            'name' => $updatedOption->name,
            'price' => intval($updatedOption->price * 100),
        ]);

        $this->assertDatabaseMissing('options', [
            'name' => $option->name,
        ]);
    }
}
