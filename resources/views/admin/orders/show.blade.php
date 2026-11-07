@extends('layouts.admin')

@section('title', 'Order ' . $order->order_number)
@section('page-title', 'Order Detail')

@section('content')
<div class="py-4 max-w-5xl">

    {{-- Back link --}}
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-900 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Orders
    </a>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900 font-mono">{{ $order->order_number }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <x-badge :variant="$order->status_color" :text="$order->status_label" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Customer + Items --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Customer Info --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="text-sm font-semibold text-slate-900 mb-4 uppercase tracking-wide text-xs">Customer Information</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-slate-500">Name</dt>
                        <dd class="font-medium text-slate-900">{{ $order->full_name }}</dd>
                    </div>
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-slate-500">Phone</dt>
                        <dd class="text-slate-800">{{ $order->phone }}</dd>
                    </div>
                    @if ($order->email)
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-slate-500">Email</dt>
                        <dd class="text-slate-800">{{ $order->email }}</dd>
                    </div>
                    @endif
                    @if ($order->user)
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-slate-500">Account</dt>
                        <dd class="text-slate-800">{{ $order->user->name }} ({{ $order->user->email }})</dd>
                    </div>
                    @else
                    <div class="flex gap-3">
                        <dt class="w-32 shrink-0 text-slate-500">Account</dt>
                        <dd class="text-slate-400 italic">Guest order</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Delivery Address --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h3 class="text-xs font-semibold text-slate-900 mb-4 uppercase tracking-wide">Delivery Address</h3>
                <div class="text-sm text-slate-700 space-y-1">
                    <p>{{ $order->address }}</p>
                    <p>{{ $order->city }}, {{ $order->district }}{{ $order->postcode ? ' - ' . $order->postcode : '' }}</p>
                    @if ($order->landmark)
                        <p class="text-slate-500">Near: {{ $order->landmark }}</p>
                    @endif
                    @if ($order->note)
                        <p class="mt-3 text-slate-500 italic">"{{ $order->note }}"</p>
                    @endif
                </div>
            </div>

            {{-- Items table --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-xs font-semibold text-slate-900 uppercase tracking-wide">Order Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                                <th class="px-5 py-3 text-left font-semibold">Product</th>
                                <th class="px-5 py-3 text-center font-semibold">Qty</th>
                                <th class="px-5 py-3 text-right font-semibold">Unit Price</th>
                                <th class="px-5 py-3 text-right font-semibold">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->items as $item)
                            @php
                                $imgSrc = str_starts_with((string) $item->product_image, 'http')
                                    ? $item->product_image
                                    : ($item->product_image
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->url($item->product_image)
                                        : asset('images/placeholder.png'));
                            @endphp
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $imgSrc }}"
                                             alt="{{ $item->product_name }}"
                                             class="w-12 h-12 object-cover rounded-lg bg-slate-100 shrink-0">
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                            @if ($item->product)
                                                <p class="text-xs text-slate-500">{{ $item->product_slug }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center text-slate-700">{{ $item->quantity }}</td>
                                <td class="px-5 py-4 text-right text-slate-700">৳{{ number_format($item->price, 0) }}</td>
                                <td class="px-5 py-4 text-right font-semibold text-slate-900">৳{{ number_format($item->subtotal, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Order totals --}}
                <div class="px-5 py-4 border-t border-slate-100 space-y-1.5 text-sm">
                    <div class="flex justify-end gap-8 text-slate-600">
                        <span>Subtotal</span>
                        <span class="w-24 text-right">৳{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-end gap-8 text-slate-600">
                        <span>Delivery</span>
                        <span class="w-24 text-right">
                            @if ((float) $order->delivery_charge === 0.0)
                                <span class="text-emerald-600">Free</span>
                            @else
                                ৳{{ number_format($order->delivery_charge, 0) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-end gap-8 font-bold text-slate-900 text-base pt-1.5 border-t border-slate-100">
                        <span>Total</span>
                        <span class="w-24 text-right">৳{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right: Status update --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sticky top-6">
                <h3 class="text-xs font-semibold text-slate-900 mb-4 uppercase tracking-wide">Update Status</h3>

                <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">Order Status</label>
                        <select id="status" name="status"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm bg-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ] as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Update Status
                    </button>
                </form>

                <div class="mt-5 pt-5 border-t border-slate-100 text-sm">
                    <p class="text-slate-500">Payment Method</p>
                    <p class="font-medium text-slate-900 mt-1 capitalize">
                        {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : strtoupper($order->payment_method) }}
                    </p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
