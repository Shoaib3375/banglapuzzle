<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class StorefrontController extends Controller
{
    /**
     * Storefront home: active categories and the latest 8 strictly-visible products.
     */
    public function home()
    {
        $categories = Category::active()
            ->withCount('subcategories')
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
     */
    public function product(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->whereHas('subcategory', fn ($q) => $q->where('is_active', true))
            ->with(['category', 'subcategory'])
            ->firstOrFail();

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
}
