<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta_title', 'BanglaPuzzle Shop')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <link rel="canonical" href="{{ url()->current() }}">
    @yield('og_tags')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    {{-- Top bar --}}
    <div class="bg-slate-900 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-9 flex items-center justify-between">
            <span>Quality products at honest prices 🇧🇩</span>
            <div class="flex items-center gap-4">
                <span>📞 +880 1700-000000</span>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-white transition-colors">Admin Panel</a>
            </div>
        </div>
    </div>

    {{-- Main header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
                        </svg>
                    </div>
                    <span class="text-indigo-700 font-bold text-xl">BanglaPuzzle</span>
                </a>

                {{-- Desktop category nav --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Categories">
                    @foreach ($navCategories as $category)
                        <a
                            href="{{ route('storefront.category', $category->slug) }}"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors
                                {{ request()->routeIs('storefront.category') && request()->route('slug') === $category->slug
                                    ? 'text-orange-600 border-b-2 border-orange-500'
                                    : 'text-slate-700 hover:text-orange-600' }}"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </nav>

                {{-- Right icons --}}
                <div class="hidden md:flex items-center gap-3">
                    <button type="button" class="p-2 text-slate-500 hover:text-indigo-600 transition-colors rounded-lg hover:bg-slate-100" aria-label="Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                    <button type="button" class="relative p-2 text-slate-500 hover:text-indigo-600 transition-colors rounded-lg hover:bg-slate-100" aria-label="Cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 bg-orange-500 text-white text-xs font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                    </button>
                </div>

                {{-- Mobile hamburger --}}
                <button
                    type="button"
                    id="nav-toggle"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
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
        <nav id="mobile-nav" class="hidden md:hidden border-t border-slate-100 bg-white" aria-label="Categories (mobile)">
            <div class="px-4 py-3 space-y-1">
                @foreach ($navCategories as $category)
                    <a
                        href="{{ route('storefront.category', $category->slug) }}"
                        class="block px-3 py-2 rounded-md text-sm font-medium transition-colors
                            {{ request()->routeIs('storefront.category') && request()->route('slug') === $category->slug
                                ? 'text-orange-600 bg-orange-50'
                                : 'text-slate-700 hover:bg-orange-50 hover:text-orange-600' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </header>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <x-alert type="success" :message="session('success')"/>
        </div>
    @endif
    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <x-alert type="error" :message="session('error')"/>
        </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                {{-- Column 1: About --}}
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-lg">BanglaPuzzle</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Quality products at honest prices. Your trusted online shopping destination in Bangladesh.</p>
                    <div class="mt-4 flex items-center gap-3">
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-indigo-600 transition-colors" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a href="#" class="w-8 h-8 bg-slate-800 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-pink-600 transition-colors" aria-label="Instagram">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Column 2: Quick Links --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="text-slate-400 hover:text-white transition-colors">Home</a></li>
                        @foreach ($navCategories as $category)
                            <li>
                                <a href="{{ route('storefront.category', $category->slug) }}" class="text-slate-400 hover:text-white transition-colors">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Column 3: Contact --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2 text-slate-400">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>info@banglapuzzle.com</span>
                        </li>
                        <li class="flex items-start gap-2 text-slate-400">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+880 1700-000000</span>
                        </li>
                        <li class="flex items-start gap-2 text-slate-400">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Dhaka, Bangladesh</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} BanglaPuzzle. All rights reserved.</p>
                <p>Made with ❤️ in Bangladesh</p>
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

    @stack('scripts')
</body>
</html>
