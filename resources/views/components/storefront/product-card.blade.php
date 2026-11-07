@props(['product'])

@php
    $imageUrl = str_starts_with((string) $product->image, 'http')
        ? $product->image
        : ($product->image
            ? Storage::disk('public')->url($product->image)
            : asset('images/placeholder.png'));

    $discount = ($product->old_price > 0 && (float)$product->old_price > (float)$product->new_price)
        ? round((($product->old_price - $product->new_price) / $product->old_price) * 100)
        : null;

    // JSON payload for Cart.add() — all values server-rendered safely
    $cartData = json_encode([
        'id'       => (string) $product->id,
        'name'     => $product->name,
        'price'    => (float) $product->new_price,
        'oldPrice' => (float) $product->old_price,
        'image'    => $imageUrl,
        'url'      => route('storefront.product', $product->slug),
        'category' => $product->category->name ?? '',
        'qty'      => 1,
    ]);
@endphp

<div class="group relative flex flex-col bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

    {{-- Discount badge --}}
    @if ($discount)
        <span class="absolute top-3 right-3 z-10 bg-red-600 text-white text-[11px] font-semibold px-2 py-0.5 rounded-full">
            -{{ $discount }}%
        </span>
    @endif

    {{-- Image — links to product detail --}}
    <a href="{{ route('storefront.product', $product->slug) }}" class="block aspect-square bg-slate-50 overflow-hidden">
        <img
            src="{{ $imageUrl }}"
            alt="{{ $product->name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
        >
    </a>

    {{-- Card body --}}
    <div class="p-4 flex flex-col flex-1">
        @if ($product->category)
            <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $product->category->name }}</p>
        @endif

        <a href="{{ route('storefront.product', $product->slug) }}"
           class="mt-1 font-semibold text-slate-900 text-sm leading-snug line-clamp-2 hover:text-blue-600 transition-colors">
            {{ $product->name }}
        </a>

        <div class="mt-2 flex items-baseline gap-2 flex-wrap">
            <span class="text-sm font-bold text-slate-900">৳{{ number_format($product->new_price, 0) }}</span>
            @if ((float) $product->old_price > (float) $product->new_price)
                <span class="text-xs text-slate-400 line-through">৳{{ number_format($product->old_price, 0) }}</span>
            @endif
        </div>

        {{-- Add to Cart button --}}
        <button
            type="button"
            onclick="cardAddToCart(this, {{ $cartData }})"
            class="mt-3 w-full flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-semibold py-2.5 min-h-[44px] rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
            aria-label="Add {{ $product->name }} to cart"
        >
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5h13M7 13L5.4 5M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            Add to Cart
        </button>
    </div>
</div>
