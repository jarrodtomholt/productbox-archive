<?php

namespace Tests\Feature\Tenant\Manage;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Admin;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;

class CategoriesTest extends TestCase
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
    public function it_returns_all_categories()
    {
        $categories = Category::factory()->count(rand(5, 8))->create();

        $response = $this->getJson(
            tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories')
        )->assertSuccessful();

        $categories->each(function ($category) use ($response) {
            $response
                ->assertSee($category->name)
                ->assertSee($category->description);
        });
    }

    /**
     * @test
     * @dataProvider categoryValidationProvider
     */
    public function it_validates_required_fields($field, $value)
    {
        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories.store'), [
            $field => $value,
        ])->assertStatus(422)->assertJsonValidationErrors($field);
    }

    public function categoryValidationProvider()
    {
        return [
            ['name', ''],
            ['description', ''],
        ];
    }

    /** @test */
    public function it_creates_a_new_category()
    {
        $category = Category::factory()->make();

        $this->postJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories.store'), $category->toArray())
            ->assertSuccessful()
            ->assertSee($category->name)
            ->assertSee($category->description);
    }

    /** @test */
    public function it_updates_an_existing_category()
    {
        $category = Category::factory()->create();

        $this->patchJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories.update', ['category' => $category]), [
            'name' => 'new category name',
            'description' => 'new category description',
        ])
        ->assertSuccessful()
        ->assertSee('new category name')
        ->assertSee('new category description');

        $this->assertDatabaseHas('categories', [
            'name' => 'new category name',
            'description' => 'new category description',
        ]);

        $this->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }

    /** @test */
    public function it_deletes_an_existing_category_that_has_no_items()
    {
        $category = Category::factory()->create();
        $this->withoutExceptionHandling();
        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories.destroy', [
            'category' => $category,
        ]))->assertSuccessful();

        $this->assertDeleted('categories', $category->toArray());
    }

    /** @test */
    public function it_fails_to_delete_an_existing_category_that_has_items()
    {
        $category = Category::factory()->create();
        Item::factory()->available()->create(['category_id' => $category]);

        $this->deleteJson(tenant_route($this->tenant->domains()->first()->domain, 'tenant.manage.categories.destroy', [
            'category' => $category,
        ]))->assertStatus(400);

        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'description' => $category->description,
        ]);
    }
}
