<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubcategoryRequest;
use App\Http\Requests\Admin\UpdateSubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of subcategories.
     */
    public function index(Request $request)
    {
        $query = Subcategory::with('category');

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $subcategories = $query->latest()->paginate(15)->appends($request->query());
        $categories    = Category::orderBy('name')->get();

        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    /**
     * Show the form for creating a new subcategory.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created subcategory.
     */
    public function store(StoreSubcategoryRequest $request)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Subcategory::generateUniqueSlug($data['name'], $data['category_id']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        Subcategory::create($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    /**
     * Show the form for editing a subcategory.
     */
    public function edit(Subcategory $subcategory)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    /**
     * Update the specified subcategory.
     */
    public function update(UpdateSubcategoryRequest $request, Subcategory $subcategory)
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Subcategory::generateUniqueSlug(
                $data['name'],
                $data['category_id'],
                $subcategory->id
            );
        }

        $data['is_active'] = $request->boolean('is_active', false);

        $subcategory->update($data);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory updated successfully.');
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy(Subcategory $subcategory)
    {
        if ($subcategory->products()->exists()) {
            return redirect()->route('admin.subcategories.index')
                ->with('error', 'Cannot delete subcategory: it has products. Remove them first.');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
    }

    /**
     * Toggle the active status of a subcategory.
     */
    public function toggleActive(Subcategory $subcategory)
    {
        $subcategory->update(['is_active' => ! $subcategory->is_active]);

        return redirect()->back()
            ->with('success', 'Subcategory status updated.');
    }

    /**
     * Return active subcategories for a given category (JSON, used by AJAX).
     */
    public function byCategory(Category $category)
    {
        $subcategories = $category->subcategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name')
            ->get();

        return response()->json($subcategories);
    }
}
