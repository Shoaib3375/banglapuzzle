@extends('layouts.storefront')

@section('meta_title', $subcategory->name . ' — ' . $subcategory->category->name)

@section('content')

    {{-- Subcategory hero banner --}}
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
                <a href="{{ route('storefront.category', $subcategory->category->slug) }}" class="hover:text-white transition-colors">
                    {{ $subcategory->category->name }}
                </a>
                <span class="text-indigo-500">›</span>
                <span class="text-white">{{ $subcategory->name }}</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold">{{ $subcategory->name }}</h1>
            @if ($subcategory->description)
                <p class="mt-3 text-indigo-200 max-w-2xl text-base">{{ $subcategory->description }}</p>
            @endif
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

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
                <h3 class="text-lg font-semibold text-gray-700">No products found</h3>
                <p class="text-gray-500 text-sm mt-1">No products in this subcategory yet. Check back soon!</p>
                <a href="{{ route('storefront.category', $subcategory->category->slug) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                    ← Back to {{ $subcategory->category->name }}
                </a>
            </div>
        @endif

    </div>
@endsection
