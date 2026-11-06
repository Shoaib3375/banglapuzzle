@extends('layouts.storefront')

@section('meta_title', $subcategory->name . ' — ' . $subcategory->category->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-indigo-700">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('storefront.category', $subcategory->category->slug) }}" class="hover:text-indigo-700">{{ $subcategory->category->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $subcategory->name }}</span>
        </nav>

        {{-- Subcategory header --}}
        <header class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $subcategory->name }}</h1>
            @if ($subcategory->description)
                <p class="mt-2 text-gray-600 max-w-3xl">{{ $subcategory->description }}</p>
            @endif
        </header>

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
                No products found in this subcategory yet.
            </div>
        @endif
    </div>
@endsection
