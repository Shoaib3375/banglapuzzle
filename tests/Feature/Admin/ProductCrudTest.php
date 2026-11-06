<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $category;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // Create a category and subcategory for use in tests
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Subcategory::factory()->create([
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_guest_cannot_access_products_index(): void
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_products_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function test_admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
    }

    public function test_admin_can_create_product_with_valid_data(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('product.jpg', 400, 400);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'Test Product',
                'description' => 'A test product description that is detailed enough.',
                'image' => $image,
                'old_price' => '500.00',
                'new_price' => '399.00',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);

        $product = Product::where('name', 'Test Product')->first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_store_fails_when_new_price_exceeds_old_price(): void
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'Overpriced Product',
                'description' => 'Description here.',
                'image' => $image,
                'old_price' => '200.00',
                'new_price' => '300.00', // > old_price
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors('new_price');
        $this->assertDatabaseMissing('products', ['name' => 'Overpriced Product']);
    }

    public function test_store_fails_when_subcategory_belongs_to_wrong_category(): void
    {
        Storage::fake('public');

        // Create a second, unrelated category
        $otherCategory = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'category_id' => $otherCategory->id,        // different category
                'subcategory_id' => $this->subcategory->id,    // belongs to $this->category
                'name' => 'Wrong Category Product',
                'description' => 'Description here.',
                'image' => $image,
                'old_price' => '200.00',
                'new_price' => '150.00',
                'is_active' => '1',
            ]);

        $response->assertSessionHasErrors('subcategory_id');
        $this->assertDatabaseMissing('products', ['name' => 'Wrong Category Product']);
    }

    public function test_store_requires_image(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.products.store'), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'No Image Product',
                'description' => 'Description here.',
                'old_price' => '200.00',
                'new_price' => '150.00',
                'is_active' => '1',
                // no image field
            ]);

        $response->assertSessionHasErrors('image');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function test_admin_can_view_edit_form(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit');
    }

    public function test_update_without_new_image_preserves_existing_image(): void
    {
        Storage::fake('public');

        // Create product with an image
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['image' => 'products/existing-image.jpg']);

        // Put a fake file at that path so the service can check it
        Storage::disk('public')->put('products/existing-image.jpg', 'fake-image-data');

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'Updated Product Name',
                'description' => 'Updated description.',
                'old_price' => '500.00',
                'new_price' => '400.00',
                'is_active' => '1',
                // no image uploaded
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('products/existing-image.jpg', $product->image);
        // Old image must still exist
        Storage::disk('public')->assertExists('products/existing-image.jpg');
    }

    public function test_update_with_new_image_replaces_old_image(): void
    {
        Storage::fake('public');

        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['image' => 'products/old-image.jpg']);

        Storage::disk('public')->put('products/old-image.jpg', 'old-image-data');

        $newImage = UploadedFile::fake()->image('new.jpg', 300, 300);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.products.update', $product), [
                'category_id' => $this->category->id,
                'subcategory_id' => $this->subcategory->id,
                'name' => 'Updated With New Image',
                'description' => 'Updated description.',
                'image' => $newImage,
                'old_price' => '500.00',
                'new_price' => '400.00',
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        // New image should be stored
        $this->assertNotNull($product->image);
        $this->assertNotEquals('products/old-image.jpg', $product->image);
        Storage::disk('public')->assertExists($product->image);
        // Old image should be deleted
        Storage::disk('public')->assertMissing('products/old-image.jpg');
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_product_and_image_is_removed(): void
    {
        Storage::fake('public');

        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['image' => 'products/to-delete.jpg']);

        Storage::disk('public')->put('products/to-delete.jpg', 'image-data');

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/to-delete.jpg');
    }

    // -------------------------------------------------------------------------
    // Toggle Active
    // -------------------------------------------------------------------------

    public function test_admin_can_toggle_product_active_status(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->post(route('admin.products.toggleActive', $product));

        $product->refresh();
        $this->assertFalse($product->is_active);
    }

    // -------------------------------------------------------------------------
    // Filtering
    // -------------------------------------------------------------------------

    public function test_search_filters_products(): void
    {
        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['name' => 'Alpha Widget']);

        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['name' => 'Beta Gadget']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['search' => 'Alpha']));

        $response->assertSee('Alpha Widget');
        $response->assertDontSee('Beta Gadget');
    }

    public function test_filter_by_category_filters_products(): void
    {
        $otherCategory = Category::factory()->create();
        $otherSub = Subcategory::factory()->create(['category_id' => $otherCategory->id]);

        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['name' => 'In Cat One']);

        Product::factory()
            ->forSubcategory($otherCategory->id, $otherSub->id)
            ->create(['name' => 'In Cat Two']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.products.index', ['category_id' => $this->category->id]));

        $response->assertSee('In Cat One');
        $response->assertDontSee('In Cat Two');
    }

    // -------------------------------------------------------------------------
    // Check Slug endpoint
    // -------------------------------------------------------------------------

    public function test_check_slug_endpoint_returns_available_true(): void
    {
        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.products.checkSlug', ['slug' => 'unique-slug-xyz']));

        $response->assertStatus(200);
        $response->assertJson(['available' => true]);
    }

    public function test_check_slug_endpoint_returns_available_false(): void
    {
        Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['slug' => 'already-taken-slug']);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.products.checkSlug', ['slug' => 'already-taken-slug']));

        $response->assertStatus(200);
        $response->assertJson(['available' => false]);
    }
}
