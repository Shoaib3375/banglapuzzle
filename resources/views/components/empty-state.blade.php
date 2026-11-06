@props([
    'title',
    'description',
    'icon',
    'actionLabel' => null,
    'actionUrl'   => null,
])

<div class="bg-white rounded-2xl shadow-sm p-16 text-center">
    <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-6">
        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
        </svg>
    </div>

    <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $title }}</h3>
    <p class="text-sm text-slate-500 mb-6">{{ $description }}</p>

    @if ($actionLabel && $actionUrl)
        <a
            href="{{ $actionUrl }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors"
        >
            {{ $actionLabel }}
        </a>
    @endif
</div>
