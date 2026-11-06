<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugHistory;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSlugRedirectTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Subcategory $subcategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create(['is_active' => true]);
        $this->subcategory = Subcategory::factory()
            ->forCategory($this->category->id)
            ->create(['is_active' => true]);
    }

    private function activeProduct(array $overrides = []): Product
    {
        return Product::factory()
            ->forSubcategory($this->category->id, $this->subcategory->id)
            ->create(array_merge(['is_active' => true], $overrides));
    }

    public function test_product_resolves_by_slug(): void
    {
        $product = $this->activeProduct(['slug' => 'active-product-slug']);

        $response = $this->get(route('storefront.product', 'active-product-slug'));

        $response->assertStatus(200);
    }

    public function test_product_returns_404_for_unknown_slug(): void
    {
        $response = $this->get(route('storefront.product', 'nonexistent-slug'));

        $response->assertStatus(404);
    }

    public function test_old_slug_redirects_301_to_current(): void
    {
        $product = $this->activeProduct(['slug' => 'current-slug']);

        ProductSlugHistory::create([
            'product_id' => $product->id,
            'old_slug' => 'old-slug',
        ]);

        $response = $this->get(route('storefront.product', 'old-slug'));

        $response->assertRedirect(route('storefront.product', 'current-slug'));
        $response->assertStatus(301);
    }

    public function test_redirect_goes_directly_to_latest_not_chain(): void
    {
        $product = $this->activeProduct(['slug' => 'latest-slug']);

        // Two historical slugs for the same product
        ProductSlugHistory::create(['product_id' => $product->id, 'old_slug' => 'first-slug']);
        ProductSlugHistory::create(['product_id' => $product->id, 'old_slug' => 'second-slug']);

        // Both old slugs should redirect directly to the latest slug
        $response = $this->get(route('storefront.product', 'first-slug'));
        $response->assertRedirect(route('storefront.product', 'latest-slug'));
        $response->assertStatus(301);

        $response2 = $this->get(route('storefront.product', 'second-slug'));
        $response2->assertRedirect(route('storefront.product', 'latest-slug'));
        $response2->assertStatus(301);
    }

    public function test_unrelated_old_slug_does_not_redirect_to_wrong_product(): void
    {
        $productA = $this->activeProduct(['slug' => 'product-a-slug']);
        $productB = $this->activeProduct(['slug' => 'product-b-slug']);

        ProductSlugHistory::create([
            'product_id' => $productA->id,
            'old_slug' => 'product-a-old-slug',
        ]);

        // Accessing product B's slug should NOT redirect to product A
        $response = $this->get(route('storefront.product', 'product-b-slug'));
        $response->assertStatus(200);

        // product-a-old-slug redirects to product A, not product B
        $response2 = $this->get(route('storefront.product', 'product-a-old-slug'));
        $response2->assertRedirect(route('storefront.product', 'product-a-slug'));
    }
}
