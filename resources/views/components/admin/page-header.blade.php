@props([
    'title',
    'createRoute' => null,
    'createLabel' => '+ New',
])

<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-semibold text-slate-800">{{ $title }}</h2>

    @if ($createRoute)
        <a
            href="{{ route($createRoute) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors"
        >
            {{ $createLabel }}
        </a>
    @endif
</div>
