<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSlugHistory;
use App\Models\Subcategory;
use App\Services\ProductImageService;
use App\Services\ProductSlugService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductImageService $imageService,
        private readonly ProductSlugService $slugService,
    ) {}

    /**
     * Display a paginated, filterable product listing.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory']);

        // Search by name
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by subcategory
        if ($subcategoryId = $request->input('subcategory_id')) {
            $query->where('subcategory_id', $subcategoryId);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('new_price', 'asc'),
            'price_desc' => $query->orderBy('new_price', 'desc'),
            default => $query->latest(),
        };

        $products = $query->paginate(15)->appends($request->query());
        $categories = Category::orderBy('name')->get();

        // For subcategory filter dropdown (populated via JS on category change)
        $subcategories = $request->input('category_id')
            ? Subcategory::where('category_id', $request->input('category_id'))->orderBy('name')->get()
            : collect();

        return view('admin.products.index', compact('products', 'categories', 'subcategories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        // Resolve slug via service (always explicit — boot() auto-slug is removed)
        if (! empty($data['slug'])) {
            $normalized = $this->slugService->normalize($data['slug']);
            $data['slug'] = $this->slugService->makeUnique($normalized, null);
        } else {
            $base = $this->slugService->generate($data['name']);
            $data['slug'] = $this->slugService->makeUnique($base, null);
        }

        $imagePath = null;

        try {
            DB::transaction(function () use ($request, &$data, &$imagePath) {
                $imagePath = $this->imageService->store($request->file('image'));
                $data['image'] = $imagePath;
                Product::create($data);
            });
        } catch (\Throwable $e) {
            $this->imageService->delete($imagePath);
            throw $e;
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $subcategories = Subcategory::where('category_id', $product->category_id)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories', 'subcategories'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $oldSlug = $product->slug;
        $newSlug = $oldSlug; // default: preserve

        if (! empty($data['slug'])) {
            $normalized = $this->slugService->normalize($data['slug']);
            $resolved = $this->slugService->makeUnique($normalized, $product->id);
            $newSlug = $resolved;
        }
        // If slug field empty/absent → keep existing slug (no auto-regenerate from name)
        $data['slug'] = $newSlug;

        $oldImagePath = $product->image;
        $newImagePath = null;

        DB::transaction(function () use ($request, $product, &$data, &$newImagePath, $oldImagePath, $oldSlug, $newSlug) {
            if ($request->hasFile('image')) {
                $newImagePath = $this->imageService->store($request->file('image'));
                $data['image'] = $newImagePath;
            } else {
                unset($data['image']);
            }

            $product->update($data);

            // Record slug history if slug changed
            if ($newSlug !== $oldSlug) {
                ProductSlugHistory::create([
                    'product_id' => $product->id,
                    'old_slug' => $oldSlug,
                ]);
            }

            if ($newImagePath && $oldImagePath) {
                $this->imageService->delete($oldImagePath);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product and its image.
     */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $imagePath = $product->image;

            $product->delete();

            $this->imageService->delete($imagePath);
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle the active status of a product.
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->back()
            ->with('success', 'Product status updated.');
    }

    /**
     * AJAX: check whether a slug is available.
     */
    public function checkSlug(Request $request): JsonResponse
    {
        $slug = $this->slugService->normalize((string) $request->input('slug', ''));
        $productId = $request->input('product_id') ? (int) $request->input('product_id') : null;

        if ($slug === '') {
            return response()->json(['available' => false, 'suggestion' => '']);
        }

        $unique = $this->slugService->makeUnique($slug, $productId);
        $available = ($unique === $slug);

        return response()->json([
            'available' => $available,
            'suggestion' => $available ? null : $unique,
        ]);
    }
}
