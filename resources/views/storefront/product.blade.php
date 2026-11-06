@extends('layouts.storefront')

@section('meta_title', $product->name)
@section('meta_description', Str::limit($product->description, 160))

@section('og_tags')
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit($product->description, 160) }}">
    @if ($product->image)
        <meta property="og:image" content="{{ Storage::disk('public')->url($product->image) }}">
    @endif
    <meta property="og:type" content="product">
@endsection

@section('content')
    @php
        $imageUrl = $product->image
            ? Storage::disk('public')->url($product->image)
            : asset('images/placeholder.png');
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-indigo-700">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('storefront.category', $product->category->slug) }}" class="hover:text-indigo-700">{{ $product->category->name }}</a>
            <span class="mx-1">/</span>
            <a href="{{ route('storefront.subcategory', [$product->category->slug, $product->subcategory->slug]) }}" class="hover:text-indigo-700">{{ $product->subcategory->name }}</a>
            <span class="mx-1">/</span>
            <span class="text-gray-700">{{ $product->name }}</span>
        </nav>

        {{-- Two-column: image (left) / details (right) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Image --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="aspect-square bg-gray-100">
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >
                </div>
            </div>

            {{-- Details --}}
            <div class="flex flex-col">
                <p class="text-sm text-gray-500">{{ $product->category->name }} &middot; {{ $product->subcategory->name }}</p>
                <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

                {{-- Prices --}}
                <div class="mt-4 flex items-baseline gap-3 flex-wrap">
                    <span class="text-3xl font-bold text-gray-900">৳{{ number_format($product->new_price, 2) }}</span>
                    @if ((float) $product->old_price > (float) $product->new_price)
                        <span class="text-lg text-gray-400 line-through">৳{{ number_format($product->old_price, 2) }}</span>
                    @endif
                    @if ($product->discount_percentage !== null)
                        <span class="inline-block rounded-full bg-red-100 text-red-700 px-3 py-1 text-sm font-semibold">
                            {{ $product->discount_percentage }}% OFF
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                <div class="mt-6 text-gray-700 whitespace-pre-line leading-relaxed">
                    {{ $product->description }}
                </div>
            </div>
        </div>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="mt-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Related Products</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($related as $relatedProduct)
                        @include('storefront.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
