@props([
    'type',
    'message',
])

@php
    $colorClasses = $type === 'success'
        ? 'bg-emerald-50 border border-emerald-200 text-emerald-800'
        : 'bg-red-50 border border-red-200 text-red-700';
@endphp

<div role="alert" class="rounded-lg px-4 py-3 text-sm flex items-start justify-between gap-3 {{ $colorClasses }}">
    <span>{{ $message }}</span>
    <button
        type="button"
        class="shrink-0 font-bold leading-none opacity-70 hover:opacity-100"
        onclick="this.closest('[role=alert]').remove()"
        aria-label="Dismiss"
    >
        &times;
    </button>
</div>
