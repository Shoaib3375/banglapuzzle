@extends('layouts.storefront')

@section('meta_title', $category->name . ' — BanglaPuzzle Shop')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-indigo-700">Home</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $category->name }}</span>
        </nav>

        {{-- Category header --}}
        <header class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-2 text-gray-600 max-w-3xl">{{ $category->description }}</p>
            @endif
        </header>

        {{-- Subcategory pills --}}
        @if ($subcategories->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach ($subcategories as $subcategory)
                    <a
                        href="{{ route('storefront.subcategory', [$category->slug, $subcategory->slug]) }}"
                        class="inline-flex items-center rounded-full border border-gray-300 bg-white px-4 py-1.5 text-sm font-medium text-gray-700 hover:border-indigo-500 hover:text-indigo-700 transition-colors"
                    >
                        {{ $subcategory->name }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Products grid --}}
        @if ($products->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    @include('storefront.partials.product-card', ['product' => $product])
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-500">
                No products found in this category yet.
            </div>
        @endif
    </div>
@endsection
