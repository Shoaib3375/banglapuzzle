<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubcategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->category = Category::factory()->create(['is_active' => true]);
    }

    public function test_index_returns_200(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.subcategories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subcategories.index');
    }

    public function test_filter_by_category(): void
    {
        $otherCategory = Category::factory()->create(['is_active' => true]);

        $inCategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['name' => 'In Category Sub']);

        $outCategory = Subcategory::factory()
            ->forCategory($otherCategory->id)
            ->create(['name' => 'Out Category Sub']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.subcategories.index', ['category_id' => $this->category->id]));

        $response->assertSee('In Category Sub');
        $response->assertDontSee('Out Category Sub');
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.subcategories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subcategories.create');
    }

    public function test_store_creates_subcategory(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.subcategories.store'), [
                'category_id' => $this->category->id,
                'name' => 'Test Sub',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.subcategories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('subcategories', [
            'name' => 'Test Sub',
            'category_id' => $this->category->id,
        ]);
    }

    public function test_composite_slug_unique_within_category(): void
    {
        // Seed an existing subcategory with slug "shared" in this category.
        Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['name' => 'Shared Sub', 'slug' => 'shared']);

        // Same slug in the SAME category must be rejected.
        $duplicate = $this->actingAs($this->admin)
            ->post(route('admin.subcategories.store'), [
                'category_id' => $this->category->id,
                'name' => 'Another Shared',
                'slug' => 'shared',
                'is_active' => '1',
            ]);

        $duplicate->assertSessionHasErrors('slug');

        // Same slug in a DIFFERENT category is allowed (uniqueness scoped to category_id).
        $otherCategory = Category::factory()->create(['is_active' => true]);

        $allowed = $this->actingAs($this->admin)
            ->post(route('admin.subcategories.store'), [
                'category_id' => $otherCategory->id,
                'name' => 'Shared Elsewhere',
                'slug' => 'shared',
                'is_active' => '1',
            ]);

        $allowed->assertRedirect(route('admin.subcategories.index'));
    }

    public function test_by_category_returns_json(): void
    {
        Subcategory::factory()->forCategory($this->category->id)->create(['is_active' => true]);
        Subcategory::factory()->forCategory($this->category->id)->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.subcategories.byCategory', $this->category));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_cannot_destroy_subcategory_with_products(): void
    {
        $subcategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create();

        Product::factory()
            ->forSubcategory($this->category->id, $subcategory->id)
            ->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.subcategories.destroy', $subcategory));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('subcategories', ['id' => $subcategory->id]);
    }

    public function test_toggle_active_flips_status(): void
    {
        $subcategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.subcategories.toggleActive', $subcategory));

        $this->assertDatabaseHas('subcategories', [
            'id' => $subcategory->id,
            'is_active' => false,
        ]);
    }
}
