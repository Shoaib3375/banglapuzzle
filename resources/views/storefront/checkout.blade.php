@extends('layouts.storefront')

@section('meta_title', 'Checkout — BanglaPuzzle')
@section('meta_description', 'Enter your delivery address and place your order.')

@section('content')
@php $authUser = Auth::user(); @endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <x-storefront.breadcrumb :items="[
        ['label' => 'Home',     'url' => route('home')],
        ['label' => 'Cart',     'url' => route('storefront.cart')],
        ['label' => 'Checkout', 'url' => null],
    ]" />

    {{-- Empty cart redirect notice --}}
    <div id="checkout-empty" class="hidden">
        <x-empty-state icon="🛒" title="Your cart is empty"
                       description="Add some items before checking out.">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                Shop Now
            </a>
        </x-empty-state>
    </div>

    {{-- Order confirmed screen (hidden until form is submitted) --}}
    <div id="order-confirmed" class="hidden max-w-lg mx-auto text-center py-16">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-900">Order Placed!</h2>
        <p class="mt-2 text-slate-500 text-sm">Thank you for your order. We'll confirm it shortly via phone or email.</p>
        <p id="confirm-ref" class="mt-3 text-xs font-mono text-slate-400"></p>
        <a href="{{ route('home') }}"
           class="mt-8 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-xl transition-colors">
            Continue Shopping
        </a>
    </div>

    {{-- Checkout layout --}}
    <div id="checkout-content" class="hidden lg:grid lg:grid-cols-3 gap-10">

        {{-- ── Left: Delivery Address Form ── --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                {{-- Step indicator --}}
                <div class="flex items-center gap-0 border-b border-slate-100">
                    <div class="flex-1 flex items-center gap-3 px-6 py-4">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center shrink-0">1</span>
                        <span class="text-sm font-semibold text-slate-900">Delivery Address</span>
                    </div>
                    <div class="flex-1 flex items-center gap-3 px-6 py-4 border-l border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-500 text-xs font-bold flex items-center justify-center shrink-0">2</span>
                        <span class="text-sm font-medium text-slate-400">Review & Pay</span>
                    </div>
                </div>

                <form id="checkout-form" class="p-6 space-y-5" method="POST" action="{{ route('orders.store') }}" novalidate>
                    @csrf

                    {{-- Full name + phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name" autocomplete="name"
                                   value="{{ old('full_name', $authUser?->name ?? '') }}"
                                   placeholder="Md Shoaib Islam"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="hidden mt-1 text-xs text-red-500" data-error="full_name">Please enter your full name.</p>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" autocomplete="tel"
                                   value="{{ old('phone', $authUser?->phone ?? '') }}"
                                   placeholder="01XXXXXXXXX"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="hidden mt-1 text-xs text-red-500" data-error="phone">Please enter a valid phone number.</p>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Email Address <span class="text-slate-400 font-normal text-xs">(optional)</span>
                        </label>
                        <input type="email" id="email" name="email" autocomplete="email"
                               value="{{ old('email', $authUser?->email ?? '') }}"
                               placeholder="shoaib@example.com"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Address line 1 --}}
                    <div>
                        <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="address" name="address" autocomplete="street-address"
                               value="{{ old('address', $authUser?->address ?? '') }}"
                               placeholder="House 12, Road 5, Block C"
                               class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <p class="hidden mt-1 text-xs text-red-500" data-error="address">Please enter your street address.</p>
                    </div>

                    {{-- City + District --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-slate-700 mb-1.5">
                                City / Area <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="city" name="city" autocomplete="address-level2"
                                   value="{{ old('city', $authUser?->city ?? '') }}"
                                   placeholder="Mirpur, Dhaka"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="hidden mt-1 text-xs text-red-500" data-error="city">Please enter your city.</p>
                        </div>
                        <div>
                            <label for="district" class="block text-sm font-medium text-slate-700 mb-1.5">
                                District <span class="text-red-500">*</span>
                            </label>
                            <select id="district" name="district"
                                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white">
                                <option value="">Select District</option>
                                @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh','Gazipur','Narayanganj','Comilla','Jessore','Bogra','Dinajpur','Tangail','Faridpur','Noakhali','Cox\'s Bazar'] as $d)
                                <option value="{{ $d }}" @selected($d === old('district', $authUser?->district ?? ''))>{{ $d }}</option>
                                @endforeach
                            </select>
                            <p class="hidden mt-1 text-xs text-red-500" data-error="district">Please select your district.</p>
                        </div>
                    </div>

                    {{-- Postcode + Landmark --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="postcode" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Post Code
                            </label>
                            <input type="text" id="postcode" name="postcode" autocomplete="postal-code"
                                   value="{{ old('postcode', $authUser?->postcode ?? '') }}"
                                   placeholder="1216"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="landmark" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Landmark <span class="text-slate-400 font-normal text-xs">(optional)</span>
                            </label>
                            <input type="text" id="landmark" name="landmark"
                                   placeholder="Near Mirpur-10 Metro"
                                   class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Delivery note --}}
                    <div>
                        <label for="note" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Order Note <span class="text-slate-400 font-normal text-xs">(optional)</span>
                        </label>
                        <textarea id="note" name="note" rows="3"
                                  placeholder="Any special instructions for delivery…"
                                  class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"></textarea>
                    </div>

                    {{-- Payment method --}}
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-2">Payment Method</p>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 transition">
                                <input type="radio" name="payment_method" value="cod" checked class="accent-blue-600">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Cash on Delivery</p>
                                    <p class="text-xs text-slate-500">Pay when your order arrives</p>
                                </div>
                                <span class="ml-auto text-xl">💵</span>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 transition">
                                <input type="radio" name="payment_method" value="bkash" class="accent-blue-600">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">bKash</p>
                                    <p class="text-xs text-slate-500">Mobile banking payment</p>
                                </div>
                                <span class="ml-auto text-xl">📱</span>
                            </label>
                            <label class="flex items-center gap-3 p-3.5 border border-slate-200 rounded-xl cursor-pointer hover:border-blue-400 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 transition">
                                <input type="radio" name="payment_method" value="nagad" class="accent-blue-600">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Nagad</p>
                                    <p class="text-xs text-slate-500">Mobile banking payment</p>
                                </div>
                                <span class="ml-auto text-xl">💳</span>
                            </label>
                        </div>
                    </div>

                    {{-- Place order button --}}
                    <button type="submit" id="place-order-btn"
                            class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-3.5 rounded-xl text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Place Order
                    </button>

                    <p class="text-center text-xs text-slate-400 mt-2">
                        🔒 Your information is safe and secure
                    </p>
                </form>
            </div>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm sticky top-24">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Order Summary</h2>

                {{-- Item list --}}
                <div id="checkout-items" class="space-y-3 mb-5"></div>

                <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>৳<span id="co-subtotal">0</span></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Delivery</span>
                        <span id="co-delivery" class="text-emerald-600 font-medium">Free</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-base pt-2 border-t border-slate-100">
                        <span>Total</span>
                        <span>৳<span id="co-total">0</span></span>
                    </div>
                </div>

                <a href="{{ route('storefront.cart') }}"
                   class="mt-5 block text-center text-xs text-blue-600 hover:text-blue-700 font-medium">
                    ← Edit Cart
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const items = Cart.getItems();

    // Redirect-like: show empty state
    const emptyEl   = document.getElementById('checkout-empty');
    const contentEl = document.getElementById('checkout-content');
    const confirmedEl = document.getElementById('order-confirmed');

    if (items.length === 0) {
        emptyEl.classList.remove('hidden');
        return;
    }

    contentEl.classList.remove('hidden');

    // ── Populate order summary ────────────────────────────────────────────────
    function fmt(n) {
        return Number(n).toLocaleString('en-BD', {minimumFractionDigits: 0, maximumFractionDigits: 0});
    }

    const subtotal = items.reduce((s, i) => s + i.price * i.qty, 0);
    const delivery = subtotal >= 2000 ? 0 : 80;
    const total    = subtotal + delivery;

    document.getElementById('co-subtotal').textContent = fmt(subtotal);
    document.getElementById('co-delivery').textContent = delivery === 0 ? 'Free' : '৳' + fmt(delivery);
    document.getElementById('co-total').textContent    = fmt(total);

    document.getElementById('checkout-items').innerHTML = items.map(item => `
        <div class="flex gap-3 items-center">
            <img src="${item.image}" alt="${item.name}"
                 class="w-12 h-12 object-cover rounded-lg bg-slate-100 shrink-0">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-slate-900 line-clamp-2">${item.name}</p>
                <p class="text-xs text-slate-500">Qty: ${item.qty}</p>
            </div>
            <p class="text-xs font-semibold text-slate-900 shrink-0">৳${fmt(item.price * item.qty)}</p>
        </div>
    `).join('');

    // ── Form validation + submission ─────────────────────────────────────────
    const form = document.getElementById('checkout-form');
    const btn  = document.getElementById('place-order-btn');

    function showError(field, show) {
        const el = form.querySelector(`[data-error="${field}"]`);
        const input = document.getElementById(field);
        if (!el || !input) return;
        if (show) {
            el.classList.remove('hidden');
            input.classList.add('border-red-400');
            input.classList.remove('border-slate-300');
        } else {
            el.classList.add('hidden');
            input.classList.remove('border-red-400');
            input.classList.add('border-slate-300');
        }
    }

    function validate() {
        let valid = true;
        const required = ['full_name', 'phone', 'address', 'city', 'district'];
        required.forEach(f => {
            const val = (document.getElementById(f)?.value || '').trim();
            const bad = val === '';
            showError(f, bad);
            if (bad) valid = false;
        });
        // phone: must be 11 digits starting with 01
        const phone = document.getElementById('phone').value.trim();
        if (phone && !/^01[3-9]\d{8}$/.test(phone)) {
            showError('phone', true);
            valid = false;
        }
        return valid;
    }

    // Live clear on input
    ['full_name','phone','address','city','district'].forEach(f => {
        const el = document.getElementById(f);
        if (el) el.addEventListener('input', () => showError(f, false));
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validate()) return;

        // Loading state
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
        </svg> Placing Order…`;

        const formData = new FormData(form);

        // Append cart items to FormData
        items.forEach(function (item, index) {
            formData.append('items[' + index + '][product_id]', item.id);
            formData.append('items[' + index + '][quantity]', item.qty);
            formData.append('items[' + index + '][price]', item.price);
        });

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { status: response.status, data: data };
            });
        })
        .then(function ({ status, data }) {
            if (status === 200 && data.success) {
                Cart.clear();
                window.location.href = data.redirect;
            } else {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg> Place Order`;

                // Show server validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(function (field) {
                        const el = form.querySelector('[data-error="' + field + '"]');
                        const input = document.getElementById(field);
                        if (el) {
                            el.textContent = data.errors[field][0];
                            el.classList.remove('hidden');
                        }
                        if (input) {
                            input.classList.add('border-red-400');
                            input.classList.remove('border-slate-300');
                        }
                    });
                } else {
                    alert(data.message || 'Something went wrong. Please try again.');
                }
            }
        })
        .catch(function () {
            btn.disabled = false;
            btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg> Place Order`;
            alert('Network error. Please check your connection and try again.');
        });
    });
});
</script>
@endpush
