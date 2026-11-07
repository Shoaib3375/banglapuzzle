@extends('layouts.admin')

@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="py-4">

    <x-admin.page-header title="Orders" />

    {{-- Status filter tabs --}}
    <div class="flex flex-wrap gap-2 mb-5">
        @php
            $statuses = [
                '' => 'All',
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'processing' => 'Processing',
                'shipped' => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
            ];
        @endphp
        @foreach ($statuses as $value => $label)
            <a href="{{ route('admin.orders.index', array_merge(request()->query(), ['status' => $value, 'page' => 1])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors
                   {{ $currentStatus === $value ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Search bar --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-5">
        @if ($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:max-w-md">
            <input
                type="text"
                name="search"
                value="{{ $searchTerm }}"
                placeholder="Search by order # or customer name…"
                class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Search
            </button>
            @if ($searchTerm)
                <a href="{{ route('admin.orders.index', $currentStatus ? ['status' => $currentStatus] : []) }}"
                   class="w-full sm:w-auto text-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-lg transition-colors">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Orders table --}}
    @if ($orders->isEmpty())
        <x-empty-state
            icon="📦"
            title="No orders found"
            description="No orders match your current filters."
        />
    @else
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-xs text-slate-500 uppercase tracking-wide">
                            <th class="px-5 py-3 text-left font-semibold">Order #</th>
                            <th class="px-5 py-3 text-left font-semibold">Customer</th>
                            <th class="px-5 py-3 text-left font-semibold">Date</th>
                            <th class="px-5 py-3 text-center font-semibold">Items</th>
                            <th class="px-5 py-3 text-right font-semibold">Total</th>
                            <th class="px-5 py-3 text-left font-semibold">Payment</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-4 font-mono text-slate-800 font-medium">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900">{{ $order->full_name }}</p>
                                @if ($order->user)
                                    <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
                                @else
                                    <p class="text-xs text-slate-400 italic">Guest</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $order->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-center text-slate-600">
                                {{ $order->items->count() }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold text-slate-900">
                                ৳{{ number_format($order->total, 0) }}
                            </td>
                            <td class="px-5 py-4 text-slate-600 capitalize">
                                {{ $order->payment_method === 'cod' ? 'COD' : strtoupper($order->payment_method) }}
                            </td>
                            <td class="px-5 py-4">
                                <x-badge :variant="$order->status_color" :text="$order->status_label" />
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
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
