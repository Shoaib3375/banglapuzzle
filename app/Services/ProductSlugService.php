<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSlugHistory;
use Illuminate\Support\Str;

class ProductSlugService
{
    /**
     * Generate a clean slug from a name string.
     * Returns 'product' if the name produces an empty slug.
     */
    public function generate(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'product';
    }

    /**
     * Normalize a manually-entered slug: lowercase, Str::slug equivalent.
     */
    public function normalize(string $slug): string
    {
        return Str::slug($slug);
    }

    /**
     * Ensure slug uniqueness.
     * Checks both products.slug (excluding $excludeProductId) and
     * product_slug_histories.old_slug to prevent recycling freed slugs.
     * Tries base, then base-2, base-3, etc.
     *
     * @param  string  $base  The candidate base slug (already normalized).
     * @param  int|null  $excludeProductId  The product ID to exclude from the products check (update case).
     */
    public function makeUnique(string $base, ?int $excludeProductId = null): string
    {
        if ($base === '') {
            $base = 'product';
        }

        $candidate = $base;
        $n = 2;

        while ($this->slugExists($candidate, $excludeProductId)) {
            $candidate = $base.'-'.$n;
            $n++;
        }

        return $candidate;
    }

    /**
     * Check if a slug is already in use (either as an active slug or in history).
     */
    private function slugExists(string $slug, ?int $excludeProductId): bool
    {
        $productQuery = Product::where('slug', $slug);
        if ($excludeProductId !== null) {
            $productQuery->where('id', '!=', $excludeProductId);
        }

        if ($productQuery->exists()) {
            return true;
        }

        return ProductSlugHistory::where('old_slug', $slug)->exists();
    }
}
