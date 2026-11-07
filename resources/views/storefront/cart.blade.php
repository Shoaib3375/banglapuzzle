@extends('layouts.storefront')

@section('meta_title', 'Your Cart — BanglaPuzzle')
@section('meta_description', 'Review your cart and proceed to checkout.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <x-storefront.breadcrumb :items="[
        ['label' => 'Home',  'url' => route('home')],
        ['label' => 'Cart',  'url' => null],
    ]" />

    <h1 class="text-2xl font-bold text-slate-900 mb-8">Shopping Cart</h1>

    {{-- Empty state (shown by JS when cart is empty) --}}
    <div id="cart-empty" class="hidden">
        <x-empty-state
            icon="🛒"
            title="Your cart is empty"
            description="Browse our products and add items to your cart."
        >
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Continue Shopping
            </a>
        </x-empty-state>
    </div>

    {{-- Cart content (shown by JS when cart has items) --}}
    <div id="cart-content" class="hidden grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Items column --}}
        <div class="lg:col-span-2 space-y-4">
            <div id="cart-items-list" class="space-y-3"></div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('home') }}"
                   class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Continue Shopping
                </a>
                <button onclick="Cart.clear()"
                        class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">
                    Clear Cart
                </button>
            </div>
        </div>

        {{-- Order summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:sticky lg:top-24">
                <h2 class="text-base font-semibold text-slate-900 mb-5">Order Summary</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal (<span id="summary-count">0</span> items)</span>
                        <span>৳<span id="summary-subtotal">0</span></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Delivery</span>
                        <span id="summary-delivery" class="text-emerald-600 font-medium">Free</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Discount</span>
                        <span id="summary-discount" class="text-red-500">—৳0</span>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex justify-between font-semibold text-slate-900 text-base">
                        <span>Total</span>
                        <span>৳<span id="summary-total">0</span></span>
                    </div>
                </div>

                <a href="{{ route('storefront.checkout') }}"
                   id="checkout-btn"
                   class="mt-6 w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 min-h-[48px] rounded-xl transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Proceed to Checkout
                </a>

                {{-- Trust strip --}}
                <div class="mt-5 pt-5 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs text-slate-500">
                    <div>🔒<br>Secure</div>
                    <div>🚚<br>Free Delivery</div>
                    <div>↩️<br>Easy Returns</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function renderCart() {
        const items    = Cart.getItems();
        const empty    = document.getElementById('cart-empty');
        const content  = document.getElementById('cart-content');
        const list     = document.getElementById('cart-items-list');

        if (items.length === 0) {
            empty.classList.remove('hidden');
            content.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        content.classList.remove('hidden');

        // Build item rows
        list.innerHTML = items.map(item => {
            const discount = item.oldPrice > item.price
                ? Math.round((item.oldPrice - item.price) / item.oldPrice * 100) : 0;
            return `
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex gap-4 items-start shadow-sm">
                <a href="${item.url}" class="shrink-0">
                    <img src="${item.image}" alt="${item.name}"
                         class="w-20 h-20 object-cover rounded-lg bg-slate-100">
                </a>
                <div class="flex-1 min-w-0">
                    <a href="${item.url}" class="text-sm font-semibold text-slate-900 hover:text-blue-600 line-clamp-2 leading-snug">${item.name}</a>
                    <p class="text-xs text-slate-500 mt-0.5">${item.category}</p>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-sm font-bold text-slate-900">৳${fmt(item.price)}</span>
                        ${item.oldPrice > item.price
                            ? `<span class="text-xs text-slate-400 line-through">৳${fmt(item.oldPrice)}</span>
                               <span class="text-xs bg-red-100 text-red-600 font-semibold px-1.5 py-0.5 rounded-full">-${discount}%</span>`
                            : ''}
                    </div>
                </div>
                <div class="flex flex-col items-end gap-3 shrink-0">
                    {{-- Qty stepper --}}
                    <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden">
                        <button onclick="Cart.update('${item.id}', ${item.qty - 1})"
                                class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors text-lg select-none"
                                ${item.qty <= 1 ? 'style="opacity:0.4"' : ''}>−</button>
                        <span class="w-9 text-center text-sm font-semibold text-slate-900 select-none">${item.qty}</span>
                        <button onclick="Cart.update('${item.id}', ${item.qty + 1})"
                                class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors text-lg select-none">+</button>
                    </div>
                    {{-- Line total --}}
                    <p class="text-sm font-semibold text-slate-900">৳${fmt(item.price * item.qty)}</p>
                    {{-- Remove --}}
                    <button onclick="Cart.remove('${item.id}')"
                            class="text-xs text-red-400 hover:text-red-600 transition-colors">Remove</button>
                </div>
            </div>`;
        }).join('');

        // Summary
        const count    = items.reduce((s, i) => s + i.qty, 0);
        const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
        const savings  = items.reduce((s, i) => s + (i.oldPrice > i.price ? (i.oldPrice - i.price) * i.qty : 0), 0);
        const delivery = subtotal >= 2000 ? 0 : 80;
        const total    = subtotal + delivery;

        document.getElementById('summary-count').textContent    = count;
        document.getElementById('summary-subtotal').textContent = fmt(subtotal);
        document.getElementById('summary-delivery').textContent = delivery === 0 ? 'Free' : '৳' + fmt(delivery);
        document.getElementById('summary-discount').textContent = savings > 0 ? '—৳' + fmt(savings) : '—৳0';
        document.getElementById('summary-total').textContent    = fmt(total);
    }

    function fmt(n) {
        return Number(n).toLocaleString('en-BD', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    window.addEventListener('cart:updated', renderCart);
    renderCart();
});
</script>
@endpush
