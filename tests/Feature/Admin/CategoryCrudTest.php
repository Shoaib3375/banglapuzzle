<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_index_accessible_to_admin(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    public function test_create_page_loads(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
    }

    public function test_store_creates_category_with_slug(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Test Category',
                'slug' => 'test-cat',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['slug' => 'test-cat']);
    }

    public function test_store_auto_generates_slug_if_omitted(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Auto Slug Cat',
                'is_active' => '1',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Auto Slug Cat',
            'slug' => 'auto-slug-cat',
        ]);
    }

    public function test_update_modifies_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.categories.update', $category), [
                'name' => 'Updated Name',
                'description' => 'd',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_destroy_removes_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_destroy_category_with_subcategories(): void
    {
        $category = Category::factory()->create();
        Subcategory::factory()->forCategory($category->id)->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.categories.destroy', $category));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_toggle_active_flips_status(): void
    {
        $category = Category::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.categories.toggleActive', $category));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_search_filters_results(): void
    {
        Category::factory()->create(['name' => 'Alpha Cat']);
        Category::factory()->create(['name' => 'Beta Cat']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index', ['search' => 'Alpha']));

        $response->assertSee('Alpha Cat');
        $response->assertDontSee('Beta Cat');
    }
}
