@extends('layouts.storefront')

@section('meta_title', 'BanglaPuzzle Shop — Home')

@section('content')

    {{-- ============================================================
         HERO BANNER
    ============================================================ --}}
    <section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900 text-white overflow-hidden" style="min-height:500px;">

        {{-- Decorative circles --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10 bg-white" style="transform:translate(30%,-30%)"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10 bg-white" style="transform:translate(-30%,30%)"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 rounded-full opacity-5 bg-orange-400" style="transform:translateY(-50%)"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 flex flex-col items-center text-center">
            {{-- Eyebrow badge --}}
            <span class="inline-flex items-center gap-2 bg-white bg-opacity-10 border border-white border-opacity-20 text-white text-xs font-semibold px-4 py-2 rounded-full mb-6">
                🇧🇩 Bangladesh's Favourite Online Shop
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight max-w-4xl">
                Shop Smart,<br>
                <span class="text-orange-400">Shop BanglaPuzzle</span>
            </h1>
            <p class="mt-6 text-lg sm:text-xl text-gray-300 max-w-2xl">
                Discover the best products at unbeatable prices. Quality you can trust, delivery you can count on.
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a
                    href="#categories"
                    class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3.5 rounded-2xl text-base transition-colors shadow-lg"
                >
                    Shop Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a
                    href="#products"
                    class="inline-flex items-center gap-2 bg-white bg-opacity-10 hover:bg-opacity-20 border border-white border-opacity-30 text-white font-semibold px-8 py-3.5 rounded-2xl text-base transition-colors"
                >
                    Browse Products
                </a>
            </div>

            {{-- Stats row --}}
            <div class="mt-14 flex flex-wrap justify-center gap-8 text-center">
                <div>
                    <p class="text-3xl font-extrabold text-white">
                        @if ($products->isNotEmpty()) {{ $products->count() }}+ @else 0 @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Products</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white">{{ $categories->count() }}+</p>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Categories</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white">100%</p>
                    <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Secure Checkout</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         USP / TRUST STRIP
    ============================================================ --}}
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 text-center sm:text-left">
                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">🚚</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Free Delivery</p>
                        <p class="text-xs text-gray-500 mt-0.5">On orders over ৳500</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 text-center sm:text-left">
                    <div class="w-12 h-12 bg-indigo-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">🔒</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Secure Payment</p>
                        <p class="text-xs text-gray-500 mt-0.5">100% protected</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 text-center sm:text-left">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">↩️</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Easy Returns</p>
                        <p class="text-xs text-gray-500 mt-0.5">7-day return policy</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 text-center sm:text-left">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">Top Quality</p>
                        <p class="text-xs text-gray-500 mt-0.5">Verified products</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

        {{-- ============================================================
             CATEGORIES SHOWCASE
        ============================================================ --}}
        @if ($categories->isNotEmpty())
            <section id="categories">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Shop by Category</h2>
                        <div class="mt-1 w-12 h-1 bg-orange-500 rounded-full"></div>
                    </div>
                </div>

                @php
                    $colorSets = [
                        ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
                        ['bg' => 'bg-rose-100',   'text' => 'text-rose-600'],
                        ['bg' => 'bg-amber-100',  'text' => 'text-amber-600'],
                        ['bg' => 'bg-emerald-100','text' => 'text-emerald-600'],
                        ['bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
                    ];
                @endphp

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach ($categories as $category)
                        @php
                            $colorSet = $colorSets[$loop->index % count($colorSets)];
                        @endphp
                        <a
                            href="{{ route('storefront.category', $category->slug) }}"
                            class="group bg-white rounded-2xl shadow-sm p-5 hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col items-center text-center"
                        >
                            <div class="w-14 h-14 {{ $colorSet['bg'] }} rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-7 h-7 {{ $colorSet['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 text-sm group-hover:text-orange-600 transition-colors">{{ $category->name }}</h3>
                            <p class="mt-1 text-xs text-gray-400">{{ $category->subcategories_count }} {{ Str::plural('subcategory', $category->subcategories_count) }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ============================================================
             PROMOTIONAL BANNER
        ============================================================ --}}
        <section class="rounded-3xl bg-gradient-to-r from-orange-500 to-red-500 overflow-hidden relative">
            <div class="absolute right-0 top-0 w-64 h-full opacity-10">
                <div class="w-64 h-64 rounded-full bg-white absolute -top-16 -right-16"></div>
                <div class="w-40 h-40 rounded-full bg-white absolute bottom-0 right-16"></div>
            </div>
            <div class="relative px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="text-center sm:text-left">
                    <p class="text-white text-xs font-bold uppercase tracking-widest mb-2">Limited Time Offer</p>
                    <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Up to 50% Off</h3>
                    <p class="text-orange-100 mt-1">On select items across all categories</p>
                </div>
                <a
                    href="#products"
                    class="flex-shrink-0 inline-flex items-center gap-2 bg-white text-orange-600 font-bold px-7 py-3 rounded-2xl hover:bg-orange-50 transition-colors shadow-md"
                >
                    Browse Deals
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </section>

        {{-- ============================================================
             LATEST PRODUCTS
        ============================================================ --}}
        <section id="products">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Latest Arrivals</h2>
                    <div class="mt-1 w-12 h-1 bg-orange-500 rounded-full"></div>
                </div>
                @if ($categories->isNotEmpty())
                    <a
                        href="{{ route('storefront.category', $categories->first()->slug) }}"
                        class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors"
                    >
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                @endif
            </div>

            @if ($products->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($products as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="No products yet"
                    description="Products are being added. Please check back soon."
                    icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                />
            @endif
        </section>

        {{-- ============================================================
             NEWSLETTER STRIP
        ============================================================ --}}
        <section class="rounded-3xl bg-indigo-600 px-8 py-12 text-center">
            <h3 class="text-2xl sm:text-3xl font-bold text-white">Stay in the Loop</h3>
            <p class="mt-2 text-indigo-200 max-w-md mx-auto">Subscribe to our newsletter and get the best deals delivered to your inbox.</p>
            <form class="mt-6 flex flex-col sm:flex-row gap-3 max-w-md mx-auto" onsubmit="return false;">
                <input
                    type="email"
                    placeholder="Enter your email address"
                    class="flex-1 rounded-xl px-4 py-3 text-sm text-gray-900 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white"
                >
                <button
                    type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl transition-colors flex-shrink-0"
                >
                    Subscribe
                </button>
            </form>
        </section>

    </div>

@endsection
