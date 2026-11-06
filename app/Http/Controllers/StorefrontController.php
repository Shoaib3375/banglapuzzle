<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugHistory;

class StorefrontController extends Controller
{
    /**
     * Storefront home: active categories and the latest 8 strictly-visible products.
     */
    public function home()
    {
        $categories = Category::active()
            ->withCount(['subcategories', 'products'])
            ->with(['subcategories' => fn ($q) => $q->where('is_active', true)->withCount('products')->orderBy('name')])
            ->orderBy('name')
            ->get();

        $products = Product::active()
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->whereHas('subcategory', fn ($q) => $q->where('is_active', true))
            ->with(['category', 'subcategory'])
            ->latest()
            ->take(8)
            ->get();

        return view('storefront.home', compact('categories', 'products'));
    }

    /**
     * Category page: only an active category is visible, with its active
     * subcategories and strictly-visible, paginated products.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::active()
            ->where('category_id', $category->id)
            ->whereHas('subcategory', fn ($q) => $q->where('is_active', true))
            ->with(['category', 'subcategory'])
            ->latest()
            ->paginate(12);

        return view('storefront.category', compact('category', 'subcategories', 'products'));
    }

    /**
     * Subcategory page: scoped to an active category and an active subcategory
     * that belongs to it, with strictly-visible, paginated products.
     */
    public function subcategory(string $categorySlug, string $subcategorySlug)
    {
        $category = Category::where('slug', $categorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $subcategory = $category->subcategories()
            ->where('slug', $subcategorySlug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::active()
            ->where('subcategory_id', $subcategory->id)
            ->with(['category', 'subcategory'])
            ->latest()
            ->paginate(12);

        return view('storefront.subcategory', compact('category', 'subcategory', 'products'));
    }

    /**
     * Product detail: only a strictly-visible product is shown, plus up to 4
     * strictly-visible related products from the same subcategory.
     * Handles 301 permanent redirects for old slugs via product_slug_histories.
     */
    public function product(string $slug)
    {
        // Try active product first
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->whereHas('subcategory', fn ($q) => $q->where('is_active', true))
            ->with(['category', 'subcategory'])
            ->first();

        if ($product) {
            $related = Product::active()
                ->where('subcategory_id', $product->subcategory_id)
                ->where('id', '!=', $product->id)
                ->whereHas('category', fn ($q) => $q->where('is_active', true))
                ->whereHas('subcategory', fn ($q) => $q->where('is_active', true))
                ->with(['category', 'subcategory'])
                ->latest()
                ->take(4)
                ->get();

            return view('storefront.product', compact('product', 'related'));
        }

        // Not found as active product — check slug history for 301 redirect
        $history = ProductSlugHistory::where('old_slug', $slug)
            ->with(['product' => fn ($q) => $q
                ->where('is_active', true)
                ->whereHas('category', fn ($q2) => $q2->where('is_active', true))
                ->whereHas('subcategory', fn ($q2) => $q2->where('is_active', true)),
            ])
            ->first();

        if ($history && $history->product) {
            // 301 permanent redirect to current slug — always the latest, no chains
            return redirect()->route('storefront.product', $history->product->slug, 301);
        }

        abort(404);
    }

    /**
     * Cart page — renders the cart shell; items are loaded from localStorage by JS.
     */
    public function cart()
    {
        return view('storefront.cart');
    }

    /**
     * Checkout page — renders the checkout shell; cart data comes from localStorage via JS.
     */
    public function checkout()
    {
        return view('storefront.checkout');
    }
}
