@php
    $imageUrl = $product->image
        ? (str_starts_with($product->image, 'http') ? $product->image : Storage::disk('public')->url($product->image))
        : asset('images/placeholder.png');
@endphp

<a
    href="{{ route('storefront.product', $product->slug) }}"
    class="group relative flex flex-col bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
>
    {{-- Discount badge --}}
    @if ($product->discount_percentage !== null)
        <span class="absolute top-2 left-2 z-10 bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full">
            {{ $product->discount_percentage }}% OFF
        </span>
    @endif

    {{-- Image area --}}
    <div class="aspect-square bg-gray-100 overflow-hidden">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
        >
    </div>

    {{-- Card body --}}
    <div class="p-4 flex flex-col flex-1">
        @if ($product->category)
            <p class="text-xs text-gray-400 uppercase tracking-wide">{{ $product->category->name }}</p>
        @endif

        <h3 class="mt-1 font-semibold text-gray-900 text-sm leading-snug line-clamp-2">{{ $product->name }}</h3>

        <div class="mt-2 flex items-baseline gap-2 flex-wrap">
            <span class="text-lg font-bold text-gray-900">৳{{ number_format($product->new_price, 2) }}</span>
            @if ((float) $product->old_price > (float) $product->new_price)
                <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->old_price, 2) }}</span>
            @endif
        </div>

        <span class="mt-3 block w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-2 rounded-xl text-center transition-colors">
            View Details
        </span>
    </div>
</a>
