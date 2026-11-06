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
            <x-storefront.breadcrumb
                :items="[
                    ['label' => 'Home', 'url' => route('home')],
                    ['label' => $category->name, 'url' => route('storefront.category', $category->slug)],
                    ['label' => $subcategory->name, 'url' => null],
                ]"
                :dark="false"
            />
            <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold">{{ $subcategory->name }}</h1>
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
                title="No products found"
                description="No products in this subcategory yet. Check back soon!"
                icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                :actionLabel="'← Back to ' . $subcategory->category->name"
                :actionUrl="route('storefront.category', $subcategory->category->slug)"
            />
        @endif

    </div>
@endsection
