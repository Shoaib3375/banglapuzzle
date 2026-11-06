@props([
    'label',
    'value',
    'subtext'   => null,
    'iconPath',
    'iconBg',
    'iconColor',
])

<div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
    <div class="shrink-0 w-12 h-12 rounded-full flex items-center justify-center {{ $iconBg }}">
        <svg class="w-6 h-6 {{ $iconColor }}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
        </svg>
    </div>

    <div class="min-w-0">
        <p class="text-sm text-slate-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-800 leading-tight">{{ $value }}</p>
        @if ($subtext)
            <p class="text-xs text-slate-400 mt-0.5">{{ $subtext }}</p>
        @endif
    </div>
</div>
