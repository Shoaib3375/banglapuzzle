@props([
    'items',
    'dark' => false,
])

@php
    $linkClass = $dark
        ? 'text-slate-500 hover:text-slate-700 hover:underline'
        : 'text-indigo-200 hover:text-white hover:underline';
    $currentClass = $dark
        ? 'text-slate-700 font-medium'
        : 'text-white font-medium';
    $separatorClass = $dark
        ? 'text-slate-400'
        : 'text-indigo-300';
@endphp

<nav aria-label="Breadcrumb">
    <ol class="flex items-center gap-1 flex-wrap text-sm">
        @foreach ($items as $index => $item)
            @if ($index > 0)
                <li class="{{ $separatorClass }}" aria-hidden="true">&rsaquo;</li>
            @endif

            <li>
                @if (!empty($item['url']) && $index < count($items) - 1)
                    <a href="{{ $item['url'] }}" class="{{ $linkClass }}">{{ $item['label'] }}</a>
                @else
                    <span class="{{ $currentClass }}">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
