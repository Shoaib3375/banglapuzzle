@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="py-4">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-3 mb-6">

        {{-- Search --}}
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name…"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 w-52"
        >

        {{-- Category filter --}}
        <select
            name="category_id"
            id="filter-category"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            <option value="">All Categories</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        {{-- Subcategory filter (populated by JS or pre-populated on page load) --}}
        <select
            name="subcategory_id"
            id="filter-subcategory"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            <option value="">All Subcategories</option>
            @foreach ($subcategories as $sub)
                <option value="{{ $sub->id }}" {{ request('subcategory_id') == $sub->id ? 'selected' : '' }}>
                    {{ $sub->name }}
                </option>
            @endforeach
        </select>

        {{-- Status filter --}}
        <select
            name="is_active"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            <option value="">All Statuses</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>

        {{-- Sort --}}
        <select
            name="sort"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
        >
            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        </select>

        <button
            type="submit"
            class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 transition-colors"
        >
            Filter
        </button>

        @if (request()->hasAny(['search', 'category_id', 'subcategory_id', 'is_active', 'sort']))
            <a
                href="{{ route('admin.products.index') }}"
                class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 transition-colors"
            >
                Clear
            </a>
        @endif

        <div class="ml-auto">
            <a
                href="{{ route('admin.products.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow"
            >
                + New Product
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Image</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Subcategory</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Old Price</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">New Price</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        {{-- Thumbnail --}}
                        <td class="px-4 py-3">
                            @if ($product->image)
                                <img
                                    src="{{ Storage::disk('public')->url($product->image) }}"
                                    alt="{{ $product->name }}"
                                    class="w-12 h-12 object-cover rounded-lg border border-gray-200"
                                >
                            @else
                                <img
                                    src="{{ asset('images/placeholder.png') }}"
                                    alt="No image"
                                    class="w-12 h-12 object-cover rounded-lg border border-gray-200 opacity-50"
                                >
                            @endif
                        </td>

                        <td class="px-4 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category?->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->subcategory?->name }}</td>
                        <td class="px-4 py-3 text-right text-gray-500">৳{{ number_format($product->old_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800">৳{{ number_format($product->new_price, 2) }}</td>

                        {{-- Status / Toggle --}}
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.products.toggleActive', $product) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-semibold transition-colors
                                        {{ $product->is_active
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                >
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a
                                href="{{ route('admin.products.edit', $product) }}"
                                class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors"
                            >
                                Edit
                            </a>
                            <form
                                method="POST"
                                action="{{ route('admin.products.destroy', $product) }}"
                                class="inline-block"
                                onsubmit="return confirm('Delete product \'{{ addslashes($product->name) }}\'? This cannot be undone.')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-lg bg-red-50 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
(function () {
    const categorySelect    = document.getElementById('filter-category');
    const subcategorySelect = document.getElementById('filter-subcategory');
    const byCategoryUrl     = '{{ route('admin.subcategories.byCategory', ['category' => '__ID__']) }}';

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const catId = this.value;

            // Reset subcategory list
            subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';

            if (!catId) return;

            const url = byCategoryUrl.replace('__ID__', catId);

            fetch(url)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    data.forEach(function (sub) {
                        const opt = document.createElement('option');
                        opt.value       = sub.id;
                        opt.textContent = sub.name;
                        subcategorySelect.appendChild(opt);
                    });
                })
                .catch(function () { /* silently ignore */ });
        });
    }
}());
</script>
@endpush
