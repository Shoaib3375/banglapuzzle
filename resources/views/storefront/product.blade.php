@extends('layouts.storefront')

@section('meta_title', $product->name)
@section('meta_description', Str::limit($product->description, 160))

@section('og_tags')
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit($product->description, 160) }}">
    @if ($product->image)
        @php
            $ogImage = str_starts_with($product->image, 'http')
                ? $product->image
                : Storage::disk('public')->url($product->image);
        @endphp
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <meta property="og:type" content="product">
@endsection

@section('content')
    @php
        $imageUrl = $product->image
            ? (str_starts_with($product->image, 'http') ? $product->image : Storage::disk('public')->url($product->image))
            : asset('images/placeholder.png');
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-500 mb-6 flex items-center gap-1 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-orange-600 transition-colors">Home</a>
            <span class="text-gray-400">›</span>
            <a href="{{ route('storefront.category', $product->category->slug) }}" class="hover:text-orange-600 transition-colors">{{ $product->category->name }}</a>
            <span class="text-gray-400">›</span>
            <a href="{{ route('storefront.subcategory', [$product->category->slug, $product->subcategory->slug]) }}" class="hover:text-orange-600 transition-colors">{{ $product->subcategory->name }}</a>
            <span class="text-gray-400">›</span>
            <span class="text-gray-700 font-medium">{{ $product->name }}</span>
        </nav>

        {{-- Two-column layout: image left, details right --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-10">

            {{-- Image column --}}
            <div class="md:col-span-2">
                <div class="rounded-2xl overflow-hidden shadow-lg bg-white">
                    <div class="aspect-square bg-gray-100">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
            </div>

            {{-- Details column --}}
            <div class="md:col-span-3 md:sticky md:top-24 flex flex-col">

                {{-- Category chips --}}
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $product->category->name }}
                    </span>
                    <span class="inline-block bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $product->subcategory->name }}
                    </span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

                {{-- Price block --}}
                <div class="mt-5 flex items-baseline gap-3 flex-wrap">
                    <span class="text-3xl font-bold text-gray-900">৳{{ number_format($product->new_price, 2) }}</span>
                    @if ((float) $product->old_price > (float) $product->new_price)
                        <span class="text-xl text-gray-400 line-through">৳{{ number_format($product->old_price, 2) }}</span>
                    @endif
                    @if ($product->discount_percentage !== null)
                        <span class="inline-block rounded-full bg-orange-100 text-orange-700 px-3 py-1 text-sm font-bold">
                            {{ $product->discount_percentage }}% OFF
                        </span>
                    @endif
                </div>

                {{-- Description --}}
                <div class="mt-6 border-t border-gray-100 pt-6 text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                {{-- Add to Cart (decorative) --}}
                <button
                    type="button"
                    class="mt-8 w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-2xl text-lg transition-colors"
                >
                    🛒 Add to Cart
                </button>

                {{-- Decorative trust icons --}}
                <div class="mt-6 flex items-center gap-6 text-xs text-gray-500">
                    <span class="flex items-center gap-1">🚚 Free Delivery</span>
                    <span class="flex items-center gap-1">🔒 Secure Payment</span>
                    <span class="flex items-center gap-1">↩️ Easy Returns</span>
                </div>

            </div>
        </div>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="mt-16 border-t border-gray-100 pt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($related as $relatedProduct)
                        @include('storefront.partials.product-card', ['product' => $relatedProduct])
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection
