@props([
    'variant' => 'gray',
    'text',
])

@php
    $colorClasses = match($variant) {
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'red'     => 'bg-red-100 text-red-700',
        'blue'    => 'bg-blue-100 text-blue-700',
        default   => 'bg-slate-100 text-slate-600',
    };
@endphp

<span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colorClasses }}">
    {{ $text }}
</span>
