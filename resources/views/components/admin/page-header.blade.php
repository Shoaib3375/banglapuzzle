@props([
    'title',
    'createRoute' => null,
    'createLabel' => '+ New',
])

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
    <h2 class="text-lg sm:text-xl font-semibold text-slate-800">{{ $title }}</h2>

    @if ($createRoute)
        <a
            href="{{ route($createRoute) }}"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors"
        >
            {{ $createLabel }}
        </a>
    @endif
</div>
