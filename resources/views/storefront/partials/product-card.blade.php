@php
    $imageUrl = $product->image
        ? Storage::disk('public')->url($product->image)
        : asset('images/placeholder.png');
@endphp

<a
    href="{{ route('storefront.product', $product->slug) }}"
    class="group flex flex-col bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow"
>
    {{-- Thumbnail --}}
    <div class="aspect-square bg-gray-100 overflow-hidden">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
        >
    </div>

    {{-- Details --}}
    <div class="p-4 flex flex-col flex-1">
        @if ($product->category)
            <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
        @endif

        <h3 class="mt-1 font-semibold text-gray-900 line-clamp-2">{{ $product->name }}</h3>

        <div class="mt-2 flex items-baseline gap-2 flex-wrap">
            @if ((float) $product->old_price > (float) $product->new_price)
                <span class="text-sm text-gray-400 line-through">৳{{ number_format($product->old_price, 2) }}</span>
            @endif
            <span class="text-lg font-bold text-gray-900">৳{{ number_format($product->new_price, 2) }}</span>
        </div>

        @if ($product->discount_percentage !== null)
            <span class="mt-2 self-start inline-block rounded-full bg-red-100 text-red-700 px-2 py-0.5 text-xs font-semibold">
                {{ $product->discount_percentage }}% OFF
            </span>
        @endif
    </div>
</a>
