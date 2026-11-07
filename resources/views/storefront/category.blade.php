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
            <x-storefront.breadcrumb
                :items="[
                    ['label' => 'Home', 'url' => route('home')],
                    ['label' => $category->name, 'url' => null],
                ]"
                :dark="false"
            />
            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold">{{ $category->name }}</h1>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <x-storefront.product-card :product="$product" />
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-10">
                    {!! $products->links() !!}
                </div>
            @endif
        @else
            <x-empty-state
                title="No products yet"
                description="No products found in this category yet. Check back soon!"
                icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                actionLabel="← Back to Home"
                :actionUrl="route('home')"
            />
        @endif

    </div>
@endsection
