@extends('layouts.storefront')

@section('meta_title', 'Order ' . $order->order_number . ' — BanglaPuzzle')
@section('meta_description', 'Your order has been placed successfully.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Success hero --}}
    <div class="text-center mb-10">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Order Placed Successfully!</h1>
        <p class="mt-2 text-slate-500 text-sm">Thank you for your order. We'll confirm it shortly via phone or email.</p>
        <p class="mt-3 text-sm font-mono text-slate-400">Order # {{ $order->order_number }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Items --}}
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-base font-semibold text-slate-900">Items Ordered</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                    @php
                        $imgSrc = str_starts_with((string) $item->product_image, 'http')
                            ? $item->product_image
                            : ($item->product_image
                                ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->product_image)
                                : asset('images/placeholder.png'));
                    @endphp
                    <div class="flex gap-4 items-center px-6 py-4">
                        <img src="{{ $imgSrc }}"
                             alt="{{ $item->product_name }}"
                             class="w-16 h-16 object-cover rounded-xl bg-slate-100 shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 line-clamp-2">{{ $item->product_name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">Qty: {{ $item->quantity }} × ৳{{ number_format($item->price, 0) }}</p>
                        </div>
                        <p class="text-sm font-semibold text-slate-900 shrink-0">৳{{ number_format($item->subtotal, 0) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Summary + Address --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Order Summary --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Order Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span>৳{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Delivery</span>
                        @if ((float) $order->delivery_charge === 0.0)
                            <span class="text-emerald-600 font-medium">Free</span>
                        @else
                            <span>৳{{ number_format($order->delivery_charge, 0) }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between font-bold text-slate-900 text-base pt-2 border-t border-slate-100">
                        <span>Total</span>
                        <span>৳{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500">
                    <span class="font-medium text-slate-700">Payment:</span>
                    <span class="ml-1 capitalize">{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : strtoupper($order->payment_method) }}</span>
                </div>
            </div>

            {{-- Delivery Address --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-slate-900 mb-3">Delivery Address</h2>
                <div class="text-sm text-slate-600 space-y-1">
                    <p class="font-medium text-slate-900">{{ $order->full_name }}</p>
                    <p>{{ $order->phone }}</p>
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->city }}, {{ $order->district }}{{ $order->postcode ? ' - ' . $order->postcode : '' }}</p>
                    @if ($order->landmark)
                        <p class="text-slate-500">Near: {{ $order->landmark }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-8 flex flex-wrap gap-3 justify-center">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-3 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Continue Shopping
        </a>
        @auth
            @if (!Auth::user()->is_admin)
                <a href="{{ route('orders.index') }}"
                   class="inline-flex items-center gap-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-6 py-3 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    My Orders
                </a>
            @endif
        @endauth
    </div>

</div>
@endsection
