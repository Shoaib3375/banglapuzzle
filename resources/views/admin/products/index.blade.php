@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="py-4">

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center mb-6">

        {{-- Search --}}
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search by name…"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 w-full sm:w-52"
        >

        {{-- Category filter --}}
        <select
            name="category_id"
            id="filter-category"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
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
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
            <option value="">All Statuses</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
        </select>

        {{-- Sort --}}
        <select
            name="sort"
            class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
            <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low → High</option>
            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High → Low</option>
        </select>

        <button
            type="submit"
            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-colors"
        >
            Filter
        </button>

        @if (request()->hasAny(['search', 'category_id', 'subcategory_id', 'is_active', 'sort']))
            <a
                href="{{ route('admin.products.index') }}"
                class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors"
            >
                Clear
            </a>
        @endif

        <div class="sm:ml-auto">
            <a
                href="{{ route('admin.products.create') }}"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm"
            >
                + New Product
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Image</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Subcategory</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Old Price</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">New Price</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-slate-50">
                        {{-- Thumbnail --}}
                        <td class="px-4 py-3">
                            @php
                                $thumb = $product->image
                                    ? (str_starts_with($product->image, 'http') ? $product->image : Storage::disk('public')->url($product->image))
                                    : asset('images/placeholder.png');
                            @endphp
                            <img
                                src="{{ $thumb }}"
                                alt="{{ $product->image ? $product->name : 'No image' }}"
                                class="w-12 h-12 object-cover rounded-lg border border-slate-200"
                            >
                        </td>

                        <td class="px-4 py-3 font-medium text-slate-900 max-w-xs truncate">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $product->category?->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $product->subcategory?->name }}</td>
                        <td class="px-4 py-3 text-right text-slate-500">৳{{ number_format($product->old_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-800">৳{{ number_format($product->new_price, 2) }}</td>

                        {{-- Status / Toggle --}}
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.products.toggleActive', $product) }}">
                                @csrf
                                <button type="submit" class="cursor-pointer">
                                    <x-badge
                                        :variant="$product->is_active ? 'emerald' : 'red'"
                                        :text="$product->is_active ? 'Active' : 'Inactive'"
                                    />
                                </button>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a
                                href="{{ route('admin.products.edit', $product) }}"
                                class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors"
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
                        <td colspan="8" class="p-0">
                            <x-empty-state
                                title="No products found"
                                description="Get started by adding your first product."
                                icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                actionLabel="+ New Product"
                                :actionUrl="route('admin.products.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
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

            subcategorySelect.innerHTML = '<option value="">All Subcategories</option>';

            if (!catId) { return; }

            const url = byCategoryUrl.replace('__ID__', catId);

            fetch(url)
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    data.forEach(function (sub) {
                        const opt       = document.createElement('option');
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
