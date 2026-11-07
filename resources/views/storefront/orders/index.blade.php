@extends('layouts.storefront')

@section('meta_title', 'My Orders — BanglaPuzzle')
@section('meta_description', 'View your order history on BanglaPuzzle.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <x-storefront.breadcrumb :items="[
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'My Orders', 'url' => null],
    ]" />

    <h1 class="text-2xl font-bold text-slate-900 mb-6">My Orders</h1>

    @if ($orders->isEmpty())
        <x-empty-state
            icon="📦"
            title="No orders yet"
            description="You haven't placed any orders yet. Start shopping!"
            actionLabel="Shop Now"
            actionUrl="{{ route('home') }}"
        />
    @else
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                            <th class="px-5 py-3 text-left font-semibold">Order #</th>
                            <th class="px-5 py-3 text-left font-semibold">Date</th>
                            <th class="px-5 py-3 text-left font-semibold">Items</th>
                            <th class="px-5 py-3 text-right font-semibold">Total</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('orders.show', $order->order_number) }}"
                                   class="font-mono text-blue-600 hover:text-blue-700 font-medium">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">
                                ৳{{ number_format($order->total, 0) }}
                            </td>
                            <td class="px-5 py-4">
                                <x-badge :variant="$order->status_color" :text="$order->status_label" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('orders.show', $order->order_number) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
