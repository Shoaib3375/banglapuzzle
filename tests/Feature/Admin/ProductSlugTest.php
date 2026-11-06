<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugHistory;
use App\Models\Subcategory;
use App\Models\User;
use App\Services\ProductSlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductSlugTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Subcategory::factory()->create([
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    private function productPayload(array $overrides = []): array
    {
        Storage::fake('public');

        return array_merge([
            'category_id' => $this->category->id,
            'subcategory_id' => $this->subcategory->id,
            'name' => 'Test Product Name',
            'description' => 'A detailed product description.',
            'image' => UploadedFile::fake()->image('p.jpg'),
            'old_price' => '500.00',
            'new_price' => '399.00',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_slug_auto_generated_from_name(): void
    {
        Storage::fake('public');
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $this->productPayload(['name' => 'My Awesome Product']));

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::where('name', 'My Awesome Product')->first();
        $this->assertNotNull($product);
        $this->assertEquals('my-awesome-product', $product->slug);
    }

    public function test_slug_accepts_manual_slug(): void
    {
        Storage::fake('public');
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $this->productPayload(['slug' => 'my-custom-slug']));

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::where('slug', 'my-custom-slug')->first();
        $this->assertNotNull($product);
        $this->assertEquals('my-custom-slug', $product->slug);
    }

    public function test_slug_rejects_duplicate(): void
    {
        Storage::fake('public');
        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'taken-slug']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $this->productPayload(['slug' => 'taken-slug']));

        $response->assertSessionHasErrors('slug');
    }

    public function test_slug_rejects_invalid_format(): void
    {
        Storage::fake('public');
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), $this->productPayload(['slug' => 'My Invalid Slug!']));

        $response->assertSessionHasErrors('slug');
    }

    public function test_slug_uniqueness_appends_suffix(): void
    {
        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'professional-walker-gadget']);

        $service = app(ProductSlugService::class);
        $unique = $service->makeUnique('professional-walker-gadget');

        $this->assertEquals('professional-walker-gadget-2', $unique);
    }

    public function test_update_preserves_slug_when_name_changes(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'original-slug']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'Completely New Name',
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '399.00',
                // no slug field
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertEquals('original-slug', $product->slug);
    }

    public function test_update_allows_manual_slug_change(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'old-slug']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => $product->name,
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '399.00',
                'slug' => 'new-slug',
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertEquals('new-slug', $product->slug);

        $this->assertDatabaseHas('product_slug_histories', [
            'product_id' => $product->id,
            'old_slug' => 'old-slug',
        ]);
    }

    public function test_update_rejects_another_products_slug(): void
    {
        $other = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'other-product-slug']);

        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'my-product-slug']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => $product->name,
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '399.00',
                'slug' => 'other-product-slug',
            ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_update_ignores_own_slug_in_uniqueness(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'my-own-slug']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => $product->name,
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '399.00',
                'slug' => 'my-own-slug',
            ]);

        $response->assertRedirect(route('admin.products.index'));
    }

    public function test_slug_history_saved_on_slug_change(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'slug-before-change']);

        $this->assertEquals(0, ProductSlugHistory::count());

        $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => $product->name,
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '399.00',
                'slug' => 'slug-after-change',
            ]);

        $this->assertEquals(1, ProductSlugHistory::count());
    }
}
