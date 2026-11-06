<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Subcategory $subcategory;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => true]);
        $this->product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => true]);
    }

    public function test_home_page_returns_200(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_home_page_does_not_show_inactive_product(): void
    {
        $inactive = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => false, 'name' => 'Hidden Inactive Product']);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee($inactive->name);
    }

    public function test_category_page_returns_200_for_active_category(): void
    {
        $response = $this->get(route('storefront.category', $this->category->slug));

        $response->assertStatus(200);
    }

    public function test_category_page_returns_404_for_inactive_category(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);

        $response = $this->get(route('storefront.category', $inactiveCategory->slug));

        $response->assertStatus(404);
    }

    public function test_category_page_returns_404_for_nonexistent_category(): void
    {
        $response = $this->get(route('storefront.category', 'no-such-slug'));

        $response->assertStatus(404);
    }

    public function test_subcategory_page_returns_200_for_active_subcategory(): void
    {
        $response = $this->get(route('storefront.subcategory', [
            $this->category->slug,
            $this->subcategory->slug,
        ]));

        $response->assertStatus(200);
    }

    public function test_subcategory_page_returns_404_for_inactive_subcategory(): void
    {
        $inactiveSub = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => false]);

        $response = $this->get(route('storefront.subcategory', [
            $this->category->slug,
            $inactiveSub->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_subcategory_page_returns_404_when_subcategory_belongs_to_different_category(): void
    {
        $otherCategory = Category::factory()->create(['is_active' => true]);

        $response = $this->get(route('storefront.subcategory', [
            $otherCategory->slug,
            $this->subcategory->slug,
        ]));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_200_for_active_product(): void
    {
        $response = $this->get(route('storefront.product', $this->product->slug));

        $response->assertStatus(200);
    }

    public function test_product_page_returns_404_for_inactive_product(): void
    {
        $inactive = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(['is_active' => false]);

        $response = $this->get(route('storefront.product', $inactive->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_404_when_category_is_inactive(): void
    {
        $inactiveCategory = Category::factory()->create(['is_active' => false]);
        $sub = Subcategory::factory()
            ->forCategory($inactiveCategory->id)
            ->create(['is_active' => true]);

        $product = Product::factory()
            ->forSubcategory($inactiveCategory->id, $sub->id)
            ->create(['is_active' => true]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_returns_404_when_subcategory_is_inactive(): void
    {
        $activeCategory = Category::factory()->create(['is_active' => true]);
        $inactiveSub = Subcategory::factory()
            ->forCategory($activeCategory->id)
            ->create(['is_active' => false]);

        $product = Product::factory()
            ->forSubcategory($activeCategory->id, $inactiveSub->id)
            ->create(['is_active' => true]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(404);
    }

    public function test_product_page_shows_discount_percentage_when_applicable(): void
    {
        $product = Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create([
                'is_active' => true,
                'old_price' => 1000,
                'new_price' => 800,
            ]);

        $response = $this->get(route('storefront.product', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('20%');
        $response->assertSee('OFF');
    }
}
