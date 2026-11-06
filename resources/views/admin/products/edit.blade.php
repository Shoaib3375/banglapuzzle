@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="py-4 max-w-3xl">

    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to Products</a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- Category --}}
            <div class="mb-5">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Category <span class="text-red-500">*</span>
                </label>
                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('category_id') border-red-400 @enderror"
                >
                    <option value="">— Select a category —</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subcategory (pre-populated from product's category) --}}
            <div class="mb-5">
                <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Subcategory <span class="text-red-500">*</span>
                </label>
                <select
                    id="subcategory_id"
                    name="subcategory_id"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('subcategory_id') border-red-400 @enderror"
                >
                    <option value="">— Select a subcategory —</option>
                    @foreach ($subcategories as $sub)
                        <option value="{{ $sub->id }}" {{ old('subcategory_id', $product->subcategory_id) == $sub->id ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
                @error('subcategory_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    maxlength="150"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('name') border-red-400 @enderror"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Slug --}}
            <div class="mb-5">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                    Slug <span class="text-gray-400 text-xs">(auto-generated if empty)</span>
                </label>
                <input
                    type="text"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $product->slug) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('slug') border-red-400 @enderror"
                >
                @error('slug')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('description') border-red-400 @enderror"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Image --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>

                @if ($product->image)
                    <div class="mb-3 flex items-center gap-3">
                        <img
                            src="{{ Storage::disk('public')->url($product->image) }}"
                            alt="{{ $product->name }}"
                            class="h-24 w-24 rounded-lg object-cover border border-gray-200"
                        >
                        <p class="text-xs text-gray-500">Current image — upload a new one to replace it.</p>
                    </div>
                @endif

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 @error('image') outline outline-red-400 rounded-lg @enderror"
                >
                <p class="mt-1 text-xs text-gray-400">JPEG, JPG, PNG, WebP — max 2 MB. Leave blank to keep the current image.</p>

                {{-- New image preview --}}
                <div id="image-preview-wrap" class="mt-2 hidden">
                    <p class="text-xs text-gray-500 mb-1">New image preview:</p>
                    <img id="image-preview" src="" alt="Preview" class="h-32 w-32 rounded-lg object-cover border border-gray-200">
                </div>
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prices --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="old_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Old Price (৳) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="old_price"
                        name="old_price"
                        value="{{ old('old_price', $product->old_price) }}"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('old_price') border-red-400 @enderror"
                    >
                    @error('old_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_price" class="block text-sm font-medium text-gray-700 mb-1">
                        New Price (৳) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="new_price"
                        name="new_price"
                        value="{{ old('new_price', $product->new_price) }}"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 @error('new_price') border-red-400 @enderror"
                    >
                    @error('new_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Is Active --}}
            <div class="mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Active (visible in storefront)</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow"
                >
                    Update Product
                </button>
                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-lg bg-gray-100 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors"
                >
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const byCategoryUrl  = '{{ route('admin.subcategories.byCategory', ['category' => '__ID__']) }}';
    const categorySelect = document.getElementById('category_id');
    const subSelect      = document.getElementById('subcategory_id');
    const nameInput      = document.getElementById('name');
    const slugInput      = document.getElementById('slug');
    const imageInput     = document.getElementById('image');
    const previewWrap    = document.getElementById('image-preview-wrap');
    const previewImg     = document.getElementById('image-preview');

    const currentCategoryId    = '{{ old('category_id', $product->category_id) }}';
    const currentSubcategoryId = '{{ old('subcategory_id', $product->subcategory_id) }}';

    // The edit form already server-renders subcategories from controller,
    // but on category change we need to reload them via AJAX.
    categorySelect.addEventListener('change', function () {
        const catId = this.value;
        subSelect.innerHTML = '<option value="">— Select a subcategory —</option>';
        if (!catId) return;

        const url = byCategoryUrl.replace('__ID__', catId);
        fetch(url)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                data.forEach(function (sub) {
                    const opt = document.createElement('option');
                    opt.value = sub.id;
                    opt.textContent = sub.name;
                    if (String(sub.id) === String(currentSubcategoryId) && String(catId) === String(currentCategoryId)) {
                        opt.selected = true;
                    }
                    subSelect.appendChild(opt);
                });
            });
    });

    // Auto-fill slug from name (only if slug is empty)
    nameInput.addEventListener('blur', function () {
        if (slugInput.value.trim() !== '') return;
        const slug = this.value.trim()
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s_]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    });

    // New image preview
    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) {
            previewWrap.classList.add('hidden');
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            previewWrap.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });
}());
</script>
@endpush
