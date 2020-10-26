<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Admin;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;

class ItemTest extends TestCase
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

    /** @test */
    public function it_returns_all_items()
    {
        $category = Category::factory()->create();
        $items = Item::factory()->count(rand(5, 10))->create(['category_id' => $category]);

        $response = $this->getJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items'))->assertSuccessful();

        $items->each(function ($item) use ($response) {
            $response
                ->assertSee($item->name)
                ->assertSee($item->description);
        });
    }

    /**
     * @test
     * @dataProvider categoryValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function categoryValidationProvider()
    {
        return [
            ['name', ''],
            ['description', ''],
            ['price', ''],
            ['price', 'asdf'],
            ['available', ''],
            ['available', 'asdf'],
        ];
    }

    /** @test */
    public function it_creates_a_new_item()
    {
        $item = Item::factory()->make();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items.store'), $item->toArray())
        ->assertSuccessful()
        ->assertSee($item->name)
        ->assertSee($item->description);

        $this->assertDatabaseHas('items', [
            'name' => $item->name,
            'description' => $item->description,
            'price' => intval($item->price * 100),
        ]);
    }

    /**
     * @test
     */
    public function it_creates_a_new_item_with_an_image()
    {
        $item = Item::factory()->make()->toArray();
        $item['image'] = UploadedFile::fake()->image('test-item-image.png');

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items'), $item);

        $item = Item::first();

        $this->assertFileExists(sprintf(
            '%s/%s/images/%s',
            config('filesystems.disks.uploads.root'),
            md5(tenant()->id),
            $item->slug . '.png'
        ));
    }

    /** @test */
    public function it_updates_an_existing_item()
    {
        $item = Item::factory()->create();

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items.update', [
            'item' => $item,
        ]), [
            'name' => 'new item name',
            'description' => 'new item description',
            'price' => '999.99',
            'available' => true,
        ])
        ->assertSuccessful()
        ->assertSee('new item name')
        ->assertSee('new item description')
        ->assertSee('999.99');

        $this->assertDatabaseHas('items', [
            'name' => 'new item name',
            'description' => 'new item description',
            'price' => '99999',
            'available' => true,
        ]);

        $this->assertDatabaseMissing('items', [
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price,
        ]);
    }

    /**
     * @test
     */
    public function it_can_update_with_an_image()
    {
        $item = Item::factory()->create();

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items.update', [
            'item' => $item,
        ]), [
            'name' => $item->name,
            'description' => $item->description,
            'price' => $item->price,
            'available' => $item->available,
            'image' => UploadedFile::fake()->image('updated-item-image.png'),
        ]);

        $this->assertFileExists(sprintf(
            '%s/%s/images/%s',
            config('filesystems.disks.uploads.root'),
            md5(tenant()->id),
            $item->fresh()->slug . '.png'
        ));
    }

    /** @test */
    public function it_deletes_an_existing_item()
    {
        $item = Item::factory()->create();

        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.items.destroy', [
            'item' => $item,
        ]))->assertSuccessful();

        $this->assertDeleted('items', $item->toArray());
    }
}
