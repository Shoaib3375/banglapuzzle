<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', 'BanglaPuzzle Shop')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- Open Graph / social tags, defined per-page via the og_tags section --}}
    @yield('og_tags')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">

    {{-- Header --}}
    <header class="bg-white shadow-sm sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Site logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-bold text-indigo-700">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>BanglaPuzzle Shop</span>
                </a>

                {{-- Desktop category nav --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Categories">
                    @foreach ($navCategories as $category)
                        <a
                            href="{{ route('storefront.category', $category->slug) }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('storefront.category') && request()->route('slug') === $category->slug
                                    ? 'text-indigo-700 bg-indigo-50'
                                    : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>

                {{-- Mobile hamburger button --}}
                <button
                    type="button"
                    id="nav-toggle"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    aria-label="Toggle navigation"
                    aria-expanded="false"
                    aria-controls="mobile-nav"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile category nav --}}
        <nav id="mobile-nav" class="hidden md:hidden border-t border-gray-200 bg-white" aria-label="Categories (mobile)">
            <div class="px-4 py-3 space-y-1">
                @foreach ($navCategories as $category)
                    <a
                        href="{{ route('storefront.category', $category->slug) }}"
                        class="block px-3 py-2 rounded-md text-sm font-medium transition-colors
                            {{ request()->routeIs('storefront.category') && request()->route('slug') === $category->slug
                                ? 'text-indigo-700 bg-indigo-50'
                                : 'text-gray-700 hover:bg-indigo-50 hover:text-indigo-700' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700" role="alert">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="text-lg font-bold text-white">BanglaPuzzle Shop</a>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} BanglaPuzzle Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Minimal JS to toggle the mobile navigation --}}
    <script>
        (function () {
            var toggle = document.getElementById('nav-toggle');
            var menu   = document.getElementById('mobile-nav');
            if (! toggle || ! menu) return;

            toggle.addEventListener('click', function () {
                var isOpen = ! menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(! isOpen));
            });
        })();
    </script>
</body>
</html>
