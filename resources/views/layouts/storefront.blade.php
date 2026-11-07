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

    {{-- Top bar removed — announcement bar is now inside each page view --}}

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

                {{-- Desktop nav: Home + Categories with hover dropdown --}}
                <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">

                    {{-- Home link --}}
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-blue-600' : 'text-slate-700 hover:text-blue-600' }}">
                        Home
                    </a>

                    {{-- Each category with JS-powered hover dropdown --}}
                    @foreach ($navCategories as $category)
                    <div class="relative"
                         onmouseenter="showDrop(this)"
                         onmouseleave="hideDrop(this)">

                        {{-- Category trigger --}}
                        <a href="{{ route('storefront.category', $category->slug) }}"
                           class="flex items-center gap-1 px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:text-blue-600 transition-colors">
                            {{ $category->name }}
                            @if($category->subcategories->isNotEmpty())
                            <svg class="w-3 h-3 mt-0.5 text-slate-400 transition-transform duration-150 drop-arrow"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                            @endif
                        </a>

                        {{-- Dropdown panel --}}
                        @if($category->subcategories->isNotEmpty())
                        <div class="nav-dropdown"
                             style="display:none;position:absolute;top:100%;left:0;margin-top:4px;width:220px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.12);padding:6px 0;z-index:9999;">
                            {{-- "All [category]" --}}
                            <a href="{{ route('storefront.category', $category->slug) }}"
                               style="display:flex;align-items:center;justify-content:space-between;padding:8px 16px;font-size:13px;font-weight:600;color:#0f172a;border-bottom:1px solid #f1f5f9;margin-bottom:4px;text-decoration:none;"
                               onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                               onmouseout="this.style.background='';this.style.color='#0f172a'">
                                All {{ $category->name }}
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                            </a>
                            {{-- Subcategory links --}}
                            @foreach($category->subcategories as $sub)
                            <a href="{{ route('storefront.subcategory', [$category->slug, $sub->slug]) }}"
                               style="display:block;padding:8px 16px;font-size:13px;color:#475569;text-decoration:none;"
                               onmouseover="this.style.background='#f8fafc';this.style.color='#0f172a'"
                               onmouseout="this.style.background='';this.style.color='#475569'">
                                {{ $sub->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </nav>

                {{-- Right icons --}}
                <div class="hidden md:flex items-center gap-3">
                    @guest
                        <a href="{{ route('user.login') }}"
                           class="text-sm font-medium text-slate-600 hover:text-blue-600 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('user.register') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                            Register
                        </a>
                    @else
                        @if (! Auth::user()->is_admin)
                        <div class="relative"
                             onmouseenter="showDrop(this)"
                             onmouseleave="hideDrop(this)">
                            <button type="button"
                                    class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-100 transition-colors">
                                <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="text-sm font-medium text-slate-700 max-w-[120px] truncate">
                                    {{ Str::limit(Auth::user()->name, 12) }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150 drop-arrow"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="nav-dropdown"
                                 style="display:none;position:absolute;top:100%;right:0;margin-top:4px;width:200px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.12);padding:6px 0;z-index:9999;">
                                <a href="{{ route('storefront.profile') }}"
                                   style="display:block;padding:8px 16px;font-size:13px;font-weight:600;color:#0f172a;text-decoration:none;border-bottom:1px solid #f1f5f9;margin-bottom:4px;"
                                   onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                   onmouseout="this.style.background='';this.style.color='#0f172a'">
                                    My Profile
                                </a>
                                <form method="POST" action="{{ route('user.logout') }}">
                                    @csrf
                                    <button type="submit"
                                            style="display:block;width:100%;text-align:left;padding:8px 16px;font-size:13px;color:#dc2626;text-decoration:none;border:none;background:none;cursor:pointer;"
                                            onmouseover="this.style.background='#fef2f2'"
                                            onmouseout="this.style.background=''">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    @endguest

                    <a href="{{ route('storefront.cart') }}"
                       class="relative p-2 text-slate-500 hover:text-blue-600 transition-colors rounded-lg hover:bg-slate-100"
                       aria-label="View cart">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span data-cart-count
                              style="display:none"
                              class="absolute -top-0.5 -right-0.5 bg-blue-600 text-white text-[10px] font-bold min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center leading-none">
                            0
                        </span>
                    </a>
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

        {{-- Mobile nav: expandable category accordion --}}
        <nav id="mobile-nav" class="hidden md:hidden border-t border-slate-100 bg-white" aria-label="Mobile navigation">
            <div class="px-4 py-3 space-y-0.5">
                {{-- Home --}}
                <a href="{{ route('home') }}"
                   class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                    Home
                </a>

                {{-- Each category: tap to expand subcategories --}}
                @foreach ($navCategories as $category)
                <div>
                    <button type="button"
                            onclick="toggleMobileCat(this)"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                        <span>{{ $category->name }}</span>
                        @if($category->subcategories->isNotEmpty())
                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M19 9l-7 7-7-7"/>
                        </svg>
                        @endif
                    </button>

                    @if($category->subcategories->isNotEmpty())
                    <div style="display:none" class="pl-4 pb-1 space-y-0.5">
                        <a href="{{ route('storefront.category', $category->slug) }}"
                           class="block px-3 py-1.5 rounded-md text-sm font-semibold text-slate-800 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                            All {{ $category->name }}
                        </a>
                        @foreach($category->subcategories as $sub)
                        <a href="{{ route('storefront.subcategory', [$category->slug, $sub->slug]) }}"
                           class="block px-3 py-1.5 rounded-md text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            {{ $sub->name }}
                        </a>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach

                {{-- Customer auth --}}
                @guest
                    <div class="border-t border-slate-100 mt-2 pt-2 space-y-0.5">
                        <a href="{{ route('user.login') }}"
                           class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ route('user.register') }}"
                           class="block px-3 py-2 rounded-md text-sm font-semibold text-blue-600 hover:bg-blue-50 transition-colors">
                            Register
                        </a>
                    </div>
                @else
                    @if (! Auth::user()->is_admin)
                        <div class="border-t border-slate-100 mt-2 pt-2 space-y-0.5">
                            <a href="{{ route('storefront.profile') }}"
                               class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                My Profile
                            </a>
                            <form method="POST" action="{{ route('user.logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    @endif
                @endguest

                {{-- Cart --}}
                <div class="border-t border-slate-100 mt-2 pt-2">
                    <a href="{{ route('storefront.cart') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Cart
                        <span data-cart-count style="display:none"
                              class="ml-auto bg-blue-600 text-white text-[10px] font-bold min-w-[18px] h-[18px] px-1 rounded-full flex items-center justify-center">0</span>
                    </a>
                </div>
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
        // Mobile nav toggle
        (function () {
            var toggle = document.getElementById('nav-toggle');
            var menu   = document.getElementById('mobile-nav');
            if (!toggle || !menu) return;
            toggle.addEventListener('click', function () {
                var isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!isOpen));
            });
        })();

        // Desktop category dropdown
        function showDrop(wrapper) {
            var drop  = wrapper.querySelector('.nav-dropdown');
            var arrow = wrapper.querySelector('.drop-arrow');
            if (drop)  drop.style.display  = 'block';
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
        function hideDrop(wrapper) {
            var drop  = wrapper.querySelector('.nav-dropdown');
            var arrow = wrapper.querySelector('.drop-arrow');
            if (drop)  drop.style.display  = 'none';
            if (arrow) arrow.style.transform = '';
        }

        // Mobile category accordion toggle
        function toggleMobileCat(btn) {
            var submenu = btn.nextElementSibling;
            var arrow   = btn.querySelector('svg');
            if (!submenu) return;
            var isOpen = submenu.style.display !== 'none' && submenu.style.display !== '';
            submenu.style.display = isOpen ? 'none' : 'block';
            if (arrow) arrow.style.transform = isOpen ? '' : 'rotate(180deg)';
        }

        // ── Shared card "Add to Cart" handler ────────────────────────────────
        // Used by x-storefront.product-card on every listing page.
        function cardAddToCart(btn, item) {
            Cart.add(item);

            // Brief success feedback on the button itself
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.classList.replace('bg-blue-600',       'bg-emerald-600');
            btn.classList.replace('hover:bg-blue-700', 'hover:bg-emerald-700');
            btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg> Added!`;

            // Toast
            const existing = document.getElementById('cart-toast');
            if (existing) existing.remove();
            const toast = document.createElement('div');
            toast.id = 'cart-toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            toast.className = 'fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-slate-900 text-white text-sm font-medium px-5 py-3.5 rounded-xl shadow-2xl opacity-0 translate-y-4 transition-all duration-300';
            const count = Cart.getCount();
            toast.innerHTML = `
                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
                <div>
                    <p>${item.name.substring(0,40)} added</p>
                    <a href="{{ route('storefront.cart') }}" class="text-xs text-blue-400 hover:text-blue-300 underline">
                        View Cart (${count} item${count !== 1 ? 's' : ''})
                    </a>
                </div>`;
            document.body.appendChild(toast);
            requestAnimationFrame(() => requestAnimationFrame(() => toast.classList.remove('opacity-0','translate-y-4')));
            setTimeout(() => { toast.classList.add('opacity-0','translate-y-4'); setTimeout(() => toast.remove(), 300); }, 3500);

            // Reset button
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = original;
                btn.classList.replace('bg-emerald-600',       'bg-blue-600');
                btn.classList.replace('hover:bg-emerald-700', 'hover:bg-blue-700');
            }, 2000);
        }
    </script>

    @stack('scripts')
</body>
</html>
