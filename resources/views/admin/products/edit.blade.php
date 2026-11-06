@extends('layouts.admin')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="py-4 max-w-3xl">

    <div class="mb-4">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Back to Products</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            {{-- Category --}}
            <div class="mb-5">
                <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1">
                    Category <span class="text-red-500">*</span>
                </label>
                <select
                    id="category_id"
                    name="category_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('category_id') border-red-400 @enderror"
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

            {{-- Subcategory (pre-populated via server-side + AJAX on change) --}}
            <div class="mb-5">
                <label for="subcategory_id" class="block text-sm font-medium text-slate-700 mb-1">
                    Subcategory <span class="text-red-500">*</span>
                </label>
                <select
                    id="subcategory_id"
                    name="subcategory_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('subcategory_id') border-red-400 @enderror"
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
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $product->name) }}"
                    maxlength="150"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- SEO & URL Settings --}}
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    SEO &amp; URL Settings
                </h3>
                <p class="text-xs text-slate-500 mb-4">The public URL for this product. Changing the slug creates a 301 redirect from the old URL.</p>

                <div class="mb-1.5 flex items-center justify-between">
                    <label for="slug" class="text-sm font-medium text-slate-700">Product Slug</label>
                    <span id="slug-mode-badge" class="text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Custom</span>
                </div>
                <div class="relative">
                    <input type="text" id="slug" name="slug"
                           value="{{ old('slug', $product->slug) }}"
                           placeholder="product-url-slug"
                           autocomplete="off"
                           class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm font-mono text-slate-800 pr-28 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('slug') border-red-400 @enderror">
                    <div id="slug-status" class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 text-xs pointer-events-none"></div>
                </div>
                @error('slug')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

                <div id="slug-change-warning" class="hidden mt-2 flex items-start gap-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    <span>Changing this URL will create a 301 permanent redirect from the old URL to the new one.</span>
                </div>

                <div class="mt-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Current URL</p>
                    <p class="text-sm font-mono text-slate-500 break-all">{{ url('/product/' . $product->slug) }}</p>
                </div>

                <div class="mt-3 bg-white border border-slate-200 rounded-lg px-4 py-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">New URL Preview</p>
                    <p class="text-sm font-mono text-blue-600 break-all" id="url-preview">{{ url('/product/' . old('slug', $product->slug)) }}</p>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-slate-700 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('description') border-red-400 @enderror"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Image --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Product Image</label>

                @if ($product->image)
                    <div class="mb-3 flex items-center gap-3">
                        @php
                            $currentThumb = str_starts_with($product->image, 'http')
                                ? $product->image
                                : Storage::disk('public')->url($product->image);
                        @endphp
                        <img
                            src="{{ $currentThumb }}"
                            alt="{{ $product->name }}"
                            class="h-24 w-24 rounded-lg object-cover border border-slate-200"
                        >
                        <p class="text-xs text-slate-500">Current image — upload a new one to replace it.</p>
                    </div>
                @endif

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100 @error('image') outline outline-red-400 rounded-lg @enderror"
                >
                <p class="mt-1 text-xs text-slate-400">JPEG, JPG, PNG, WebP — max 2 MB. Leave blank to keep the current image.</p>

                {{-- New image preview --}}
                <div id="image-preview-wrap" class="mt-2 hidden">
                    <p class="text-xs text-slate-500 mb-1">New image preview:</p>
                    <img id="image-preview" src="" alt="Preview" class="h-32 w-32 rounded-lg object-cover border border-slate-200">
                </div>
                @error('image')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prices --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                <div>
                    <label for="old_price" class="block text-sm font-medium text-slate-700 mb-1">
                        Old Price (৳) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="old_price"
                        name="old_price"
                        value="{{ old('old_price', $product->old_price) }}"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('old_price') border-red-400 @enderror"
                    >
                    @error('old_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_price" class="block text-sm font-medium text-slate-700 mb-1">
                        New Price (৳) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="new_price"
                        name="new_price"
                        value="{{ old('new_price', $product->new_price) }}"
                        min="0"
                        step="0.01"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('new_price') border-red-400 @enderror"
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
                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                    >
                    <span class="text-sm font-medium text-slate-700">Active (visible in storefront)</span>
                </label>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm"
                >
                    Update Product
                </button>
                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-lg bg-slate-100 px-6 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors"
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
    const checkSlugUrl   = '{{ route('admin.products.checkSlug') }}';
    const baseProductUrl = '{{ url('/product/') }}';
    const originalSlug   = '{{ $product->slug }}';
    const productId      = '{{ $product->id }}';

    const categorySelect    = document.getElementById('category_id');
    const subSelect         = document.getElementById('subcategory_id');
    const slugInput         = document.getElementById('slug');
    const slugStatus        = document.getElementById('slug-status');
    const urlPreview        = document.getElementById('url-preview');
    const slugChangeWarning = document.getElementById('slug-change-warning');
    const imageInput        = document.getElementById('image');
    const previewWrap       = document.getElementById('image-preview-wrap');
    const previewImg        = document.getElementById('image-preview');

    const currentCategoryId    = '{{ old('category_id', $product->category_id) }}';
    const currentSubcategoryId = '{{ old('subcategory_id', $product->subcategory_id) }}';

    let slugCheckTimer = null;

    function updatePreview(slugValue) {
        urlPreview.textContent = baseProductUrl + '/' + (slugValue || '');
    }

    function showWarning(show) {
        if (show) {
            slugChangeWarning.classList.remove('hidden');
        } else {
            slugChangeWarning.classList.add('hidden');
        }
    }

    // Slug changes on edit — name does NOT auto-update slug
    slugInput.addEventListener('input', function () {
        const slug = this.value;
        updatePreview(slug);
        showWarning(slug !== originalSlug && slug.trim() !== '');
        clearTimeout(slugCheckTimer);
    });

    // Debounced availability check on blur
    slugInput.addEventListener('blur', function () {
        clearTimeout(slugCheckTimer);
        const slug = this.value.trim();
        if (!slug || slug === originalSlug) { slugStatus.innerHTML = ''; return; }
        slugCheckTimer = setTimeout(function () {
            slugStatus.innerHTML = '<span class="text-slate-400">Checking\u2026</span>';
            fetch(checkSlugUrl + '?slug=' + encodeURIComponent(slug) + '&product_id=' + productId)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.available) {
                        slugStatus.innerHTML = '<span class="text-emerald-600">\u2713 Available</span>';
                    } else {
                        slugStatus.innerHTML = '<span class="text-red-500">\u2717 Already taken</span>';
                    }
                })
                .catch(function () { slugStatus.innerHTML = ''; });
        }, 400);
    });

    // Category/subcategory
    categorySelect.addEventListener('change', function () {
        const catId = this.value;
        subSelect.innerHTML = '<option value="">— Select a subcategory —</option>';
        if (!catId) { return; }
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

    // Image preview
    imageInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) { previewWrap.classList.add('hidden'); return; }
        const reader = new FileReader();
        reader.onload = function (e) { previewImg.src = e.target.result; previewWrap.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    });
}());
</script>
@endpush
