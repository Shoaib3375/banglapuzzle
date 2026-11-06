@extends('layouts.storefront')

@section('meta_title', $category->name . ' — BanglaPuzzle Shop')

@section('content')

    {{-- Category hero banner --}}
    <section class="bg-gradient-to-br from-indigo-700 to-indigo-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white" style="transform:translate(30%,-30%)"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 rounded-full bg-white" style="transform:translate(-20%,20%)"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            {{-- Breadcrumb --}}
            <nav class="text-sm text-indigo-300 mb-4 flex items-center gap-1 flex-wrap" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a>
                <span class="text-indigo-500">›</span>
                <span class="text-white">{{ $category->name }}</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-3 text-indigo-200 max-w-2xl text-base">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Subcategory filter pills --}}
        @if ($subcategories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-10">
                @foreach ($subcategories as $subcategory)
                    @php
                        $isActive = request()->routeIs('storefront.subcategory')
                            && request()->route('subcategorySlug') === $subcategory->slug;
                    @endphp
                    <a
                        href="{{ route('storefront.subcategory', [$category->slug, $subcategory->slug]) }}"
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border transition-colors
                            {{ $isActive
                                ? 'bg-orange-500 text-white border-orange-500'
                                : 'bg-white text-gray-700 border-gray-300 hover:border-orange-400 hover:text-orange-600' }}"
                    >
                        {{ $subcategory->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Products grid --}}
        @if ($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-10">
                    {!! $products->links() !!}
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700">No products yet</h3>
                <p class="text-gray-500 text-sm mt-1">No products found in this category yet. Check back soon!</p>
                <a href="{{ route('home') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                    ← Back to Home
                </a>
            </div>
        @endif

    </div>
@endsection
