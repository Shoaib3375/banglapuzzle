@extends('layouts.storefront')

@section('meta_title', 'BanglaPuzzle Shop — Home')

@section('content')
    {{-- Hero banner --}}
    <section class="bg-slate-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Welcome to BanglaPuzzle Shop</h1>
            <p class="mt-3 text-slate-300 max-w-2xl mx-auto">Discover great deals across our categories. Quality products, honest prices.</p>
            <div class="mt-6">
                <a href="#categories" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                    Browse Categories
                </a>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Category cards --}}
        @if ($categories->isNotEmpty())
            <section id="categories">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Shop by Category</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($categories as $category)
                        <a
                            href="{{ route('storefront.category', $category->slug) }}"
                            class="group bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-shadow flex flex-col"
                        >
                            <span class="text-gray-400 group-hover:text-indigo-600 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </span>
                            <h3 class="mt-3 font-semibold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $category->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $category->subcategories_count }} {{ Str::plural('Subcategory', $category->subcategories_count) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Latest products --}}
        <section class="mt-12">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Latest Products</h2>

            @if ($products->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        @include('storefront.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-500">
                    No products are available right now. Please check back soon.
                </div>
            @endif
        </section>
    </div>
@endsection
