@extends('layouts.storefront')

@section('meta_title', $product->name . ' | Bangla Puzzle')
@section('meta_description', Str::limit($product->description, 160))

@section('og_tags')
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit($product->description, 160) }}">
    @php
        $ogImage = str_starts_with((string) $product->image, 'http')
            ? $product->image
            : ($product->image
                ? Storage::disk('public')->url($product->image)
                : asset('images/placeholder.png'));
    @endphp
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:type" content="product">
    <meta property="og:url" content="{{ route('storefront.product', $product->slug) }}">
@endsection

@section('content')
    @php
        $imageUrl = str_starts_with((string) $product->image, 'http')
            ? $product->image
            : ($product->image
                ? Storage::disk('public')->url($product->image)
                : asset('images/placeholder.png'));
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Breadcrumb --}}
        <x-storefront.breadcrumb
            :items="[
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $product->category->name, 'url' => route('storefront.category', $product->category->slug)],
                ['label' => $product->subcategory->name, 'url' => route('storefront.subcategory', [$product->category->slug, $product->subcategory->slug])],
                ['label' => $product->name, 'url' => null],
            ]"
            :dark="true"
        />

        {{-- Two-column layout: image left, details right --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-10">

            {{-- Image column --}}
            <div class="">
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
            <div class="lg:sticky lg:top-24 flex flex-col">

                {{-- Category chips --}}
                <div class="flex items-center gap-2 flex-wrap mb-3">
                    <x-badge variant="blue" :text="$product->category->name" />
                    <x-badge variant="gray" :text="$product->subcategory->name" />
                </div>

                <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>

                {{-- Price block --}}
                <div class="mt-5 flex items-baseline gap-3 flex-wrap">
                    <span class="text-3xl font-bold text-gray-900">৳{{ number_format($product->new_price, 2) }}</span>
                    @if ((float) $product->old_price > (float) $product->new_price)
                        <span class="text-xl text-gray-400 line-through">৳{{ number_format($product->old_price, 2) }}</span>
                    @endif
                    @if ($product->discount_percentage !== null)
                        <x-badge variant="red" :text="$product->discount_percentage . '% OFF'" />
                    @endif
                </div>

                {{-- Description --}}
                <div class="mt-6 border-t border-gray-100 pt-6 text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                {{-- Quantity selector + Add to Cart --}}
                <div class="mt-8 space-y-4">

                    {{-- Quantity --}}
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-slate-700">Quantity</span>
                        <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden">
                            <button
                                type="button"
                                id="qty-decrease"
                                onclick="changeQty(-1)"
                                class="w-11 h-11 flex items-center justify-center text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors text-xl font-light select-none"
                                aria-label="Decrease quantity"
                            >−</button>
                            <input
                                type="number"
                                id="qty-input"
                                value="1"
                                min="1"
                                max="99"
                                readonly
                                class="w-14 h-11 text-center text-base font-semibold text-slate-900 border-x border-slate-300 bg-white focus:outline-none"
                                aria-label="Quantity"
                            >
                            <button
                                type="button"
                                id="qty-increase"
                                onclick="changeQty(1)"
                                class="w-11 h-11 flex items-center justify-center text-slate-600 hover:bg-slate-100 active:bg-slate-200 transition-colors text-xl font-light select-none"
                                aria-label="Increase quantity"
                            >+</button>
                        </div>
                    </div>

                    {{-- Add to Cart button --}}
                    <button
                        type="button"
                        id="add-to-cart-btn"
                        onclick="addToCart()"
                        class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3.5 px-8 min-h-[48px] rounded-xl text-base transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        aria-label="Add to cart"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5h13M7 13L5.4 5M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/>
                        </svg>
                        Add to Cart
                    </button>
                </div>

                {{-- Trust icons --}}
                <div class="mt-6 grid grid-cols-3 gap-3">
                    <div class="flex flex-col items-center text-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-lg mb-1">🚚</span>
                        <span class="text-xs font-medium text-slate-600">Free Delivery</span>
                    </div>
                    <div class="flex flex-col items-center text-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-lg mb-1">🔒</span>
                        <span class="text-xs font-medium text-slate-600">Secure Payment</span>
                    </div>
                    <div class="flex flex-col items-center text-center p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-lg mb-1">↩️</span>
                        <span class="text-xs font-medium text-slate-600">Easy Returns</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Related products --}}
        @if ($related->isNotEmpty())
            <section class="mt-16 border-t border-gray-100 pt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($related as $relatedProduct)
                        <x-storefront.product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection

@push('scripts')
<script>
    // ── Quantity control ──────────────────────────────────────────────────────
    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        const current = parseInt(input.value, 10) || 1;
        const next = Math.max(1, Math.min(99, current + delta));
        input.value = next;
        document.getElementById('qty-decrease').style.opacity = next === 1 ? '0.4' : '1';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('qty-decrease').style.opacity = '0.4';
    });

    // ── Add to Cart — wired to Cart store ────────────────────────────────────
    function addToCart() {
        const qty  = parseInt(document.getElementById('qty-input').value, 10) || 1;
        const btn  = document.getElementById('add-to-cart-btn');

        // Pull product data embedded by Blade
        const product = {
            id:       '{{ $product->id }}',
            name:     {{ Js::from($product->name) }},
            price:    {{ (float) $product->new_price }},
            oldPrice: {{ (float) $product->old_price }},
            image:    {{ Js::from($imageUrl) }},
            url:      '{{ route('storefront.product', $product->slug) }}',
            category: {{ Js::from($product->category->name ?? '') }},
            qty:      qty,
        };

        Cart.add(product);

        // Loading state
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg> Adding…`;

        setTimeout(function () {
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg> Added to Cart!`;
            btn.classList.replace('bg-blue-600',       'bg-emerald-600');
            btn.classList.replace('hover:bg-blue-700', 'hover:bg-emerald-700');
            btn.disabled = false;

            showToast(qty + ' × ' + product.name.substring(0, 40) + ' added to cart');

            setTimeout(function () {
                btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5h13M7 13L5.4 5M10 21a1 1 0 100-2 1 1 0 000 2zm7 0a1 1 0 100-2 1 1 0 000 2z"/></svg> Add to Cart`;
                btn.classList.replace('bg-emerald-600',       'bg-blue-600');
                btn.classList.replace('hover:bg-emerald-700', 'hover:bg-blue-700');
            }, 2500);
        }, 500);
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    function showToast(message) {
        const existing = document.getElementById('cart-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'cart-toast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.className = 'fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-slate-900 text-white text-sm font-medium px-5 py-3.5 rounded-xl shadow-2xl opacity-0 translate-y-4 transition-all duration-300';

        const count = Cart.getCount();
        toast.innerHTML = `
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
            <div>
                <p>${message}</p>
                <a href="{{ route('storefront.cart') }}" class="text-xs text-blue-400 hover:text-blue-300 underline">
                    View Cart (${count} item${count !== 1 ? 's' : ''})
                </a>
            </div>`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => requestAnimationFrame(() => {
            toast.classList.remove('opacity-0', 'translate-y-4');
        }));

        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
</script>
@endpush
