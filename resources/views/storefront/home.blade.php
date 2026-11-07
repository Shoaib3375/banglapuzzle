@extends('layouts.storefront')

@section('meta_title', 'BanglaPuzzle — Find Something You\'ll Love')
@section('meta_description', 'Explore thoughtfully selected products across our collections. Quality you can trust.')

@section('og_tags')
<meta property="og:title" content="BanglaPuzzle — Find Something You'll Love">
<meta property="og:description" content="Explore thoughtfully selected products across our collections.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     A. ANNOUNCEMENT BAR
══════════════════════════════════════════════════════════════════ --}}
<div class="bg-slate-900 text-slate-300 text-xs text-center py-2.5 px-4 tracking-wide">
    <span>✦ Discover our latest collections &nbsp;·&nbsp; Free delivery on orders over ৳2,000 ✦</span>
</div>

{{-- ══════════════════════════════════════════════════════════════════
     B. HERO SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="bg-white overflow-hidden" aria-label="Hero">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Left: copy --}}
            <div class="order-2 lg:order-1">
                <p class="inline-flex items-center gap-2 text-xs font-semibold tracking-widest text-blue-600 uppercase mb-5">
                    <span class="w-6 h-px bg-blue-600 inline-block"></span>
                    Welcome to BanglaPuzzle
                </p>

                <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-bold text-slate-900 leading-[1.1] tracking-tight">
                    Find Something<br>
                    <span class="text-blue-600">You'll Love.</span>
                </h1>

                <p class="mt-5 text-base sm:text-lg text-slate-500 leading-relaxed max-w-lg">
                    Explore thoughtfully selected products across our collections — from everyday essentials to unique finds.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#products"
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg text-sm transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Explore Products
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#categories"
                       class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 font-semibold px-6 py-3 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                        Browse Categories
                    </a>
                </div>

                {{-- Trust micro-indicators --}}
                <div class="mt-10 flex items-center gap-5 flex-wrap">
                    <div class="flex items-center gap-1.5">
                        <div class="flex -space-x-1.5">
                            @foreach(['bg-blue-400','bg-emerald-400','bg-amber-400','bg-rose-400'] as $c)
                            <div class="w-7 h-7 rounded-full {{ $c }} border-2 border-white"></div>
                            @endforeach
                        </div>
                        <span class="text-xs text-slate-500 font-medium">1,000+ happy customers</span>
                    </div>
                    <div class="flex items-center gap-1 text-amber-400">
                        @for($i=0;$i<5;$i++)<svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                        <span class="text-xs text-slate-500 font-medium ml-1">4.9 / 5</span>
                    </div>
                </div>
            </div>

            {{-- Right: product image mosaic --}}
            <div class="order-1 lg:order-2 relative">
                {{-- Background blob --}}
                <div class="absolute inset-0 bg-slate-100 rounded-3xl -rotate-3 scale-95 opacity-50"></div>
                @if($products->count() >= 4)
                <div class="relative grid grid-cols-2 gap-3 p-2">
                    @foreach($products->take(4) as $i => $p)
                    @php
                        $imgSrc = $p->image
                            ? (str_starts_with($p->image,'http') ? $p->image : Storage::disk('public')->url($p->image))
                            : asset('images/placeholder.png');
                    @endphp
                    <a href="{{ route('storefront.product', $p->slug) }}"
                       class="group relative overflow-hidden rounded-2xl bg-slate-100 aspect-square shadow-sm hover:shadow-md transition-shadow">
                        <img src="{{ $imgSrc }}" alt="{{ $p->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="{{ $i < 2 ? 'eager' : 'lazy' }}">
                        @if($p->discount_percentage)
                        <span class="absolute top-2 right-2 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                            -{{ $p->discount_percentage }}%
                        </span>
                        @endif
                    </a>
                    @endforeach
                </div>
                @else
                {{-- Fallback when fewer than 4 products --}}
                <div class="relative rounded-3xl overflow-hidden aspect-square bg-gradient-to-br from-blue-50 to-slate-100 flex items-center justify-center">
                    <div class="text-center p-8">
                        <div class="text-7xl mb-4">🛍️</div>
                        <p class="text-slate-500 font-medium">Products loading…</p>
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     USP / BENEFITS STRIP
══════════════════════════════════════════════════════════════════ --}}
<section class="border-y border-slate-200 bg-slate-50" aria-label="Benefits">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12 py-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-slate-200">
            @foreach([
                ['icon'=>'🚚','title'=>'Carefully Selected','desc'=>'Curated quality products'],
                ['icon'=>'🔍','title'=>'Easy Discovery','desc'=>'Find products fast'],
                ['icon'=>'💬','title'=>'Customer Support','desc'=>'We\'re here to help'],
                ['icon'=>'🔒','title'=>'Secure Shopping','desc'=>'Safe & trusted experience'],
            ] as $b)
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 first:pl-0 last:pr-0">
                <span class="text-2xl shrink-0" aria-hidden="true">{{ $b['icon'] }}</span>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $b['title'] }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $b['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="max-w-screen-xl mx-auto px-6 lg:px-12">

    {{-- ══════════════════════════════════════════════════════════════
         D. FEATURED CATEGORIES
    ══════════════════════════════════════════════════════════════ --}}
    @if($categories->isNotEmpty())
    <section id="categories" class="py-16 lg:py-20" aria-labelledby="categories-heading">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-10">
            <div>
                <p class="text-xs font-semibold tracking-widest text-blue-600 uppercase mb-2">Collections</p>
                <h2 id="categories-heading" class="text-3xl font-bold text-slate-900">Explore Categories</h2>
                <p class="mt-2 text-slate-500 text-sm">Find exactly what you're looking for.</p>
            </div>
        </div>

        @php
        $palettes = [
            ['bg'=>'bg-blue-50',   'ring'=>'ring-blue-200',   'icon'=>'bg-blue-100',   'text'=>'text-blue-600'],
            ['bg'=>'bg-violet-50', 'ring'=>'ring-violet-200', 'icon'=>'bg-violet-100', 'text'=>'text-violet-600'],
            ['bg'=>'bg-rose-50',   'ring'=>'ring-rose-200',   'icon'=>'bg-rose-100',   'text'=>'text-rose-600'],
            ['bg'=>'bg-amber-50',  'ring'=>'ring-amber-200',  'icon'=>'bg-amber-100',  'text'=>'text-amber-600'],
            ['bg'=>'bg-emerald-50','ring'=>'ring-emerald-200','icon'=>'bg-emerald-100','text'=>'text-emerald-600'],
            ['bg'=>'bg-cyan-50',   'ring'=>'ring-cyan-200',   'icon'=>'bg-cyan-100',   'text'=>'text-cyan-600'],
        ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $category)
            @php $pal = $palettes[$loop->index % count($palettes)]; @endphp
            <a href="{{ route('storefront.category', $category->slug) }}"
               class="group relative {{ $pal['bg'] }} rounded-2xl p-5 ring-1 {{ $pal['ring'] }} hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col items-center text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                {{-- Icon circle --}}
                <div class="w-14 h-14 {{ $pal['icon'] }} rounded-2xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-7 h-7 {{ $pal['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900 text-sm leading-snug group-hover:text-blue-600 transition-colors">{{ $category->name }}</h3>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $category->products_count ?? 0 }} {{ Str::plural('product', $category->products_count ?? 0) }}
                </p>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         E. LATEST PRODUCTS
    ══════════════════════════════════════════════════════════════ --}}
    <section id="products" class="pb-16 lg:pb-20" aria-labelledby="products-heading">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-10">
            <div>
                <p class="text-xs font-semibold tracking-widest text-blue-600 uppercase mb-2">New In</p>
                <h2 id="products-heading" class="text-3xl font-bold text-slate-900">Discover Our Products</h2>
                <p class="mt-2 text-slate-500 text-sm">The latest additions across all categories.</p>
            </div>
            @if($categories->isNotEmpty())
            <a href="{{ route('storefront.category', $categories->first()->slug) }}"
               class="self-start sm:self-auto inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors shrink-0">
                View all products
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            @endif
        </div>

        @if($products->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach($products as $product)
                <x-storefront.product-card :product="$product" />
            @endforeach
        </div>
        @else
        <x-empty-state
            icon="📦"
            title="No products yet"
            description="Products are being added. Check back soon."
        >
            <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Refresh page</a>
        </x-empty-state>
        @endif
    </section>

    {{-- ══════════════════════════════════════════════════════════════
         F. PROMOTIONAL BANNER
    ══════════════════════════════════════════════════════════════ --}}
    <section class="mb-16 lg:mb-20" aria-label="Promotion">
        <div class="relative overflow-hidden rounded-2xl bg-blue-600">
            {{-- Subtle pattern overlay --}}
            <div class="absolute inset-0 opacity-[0.07]" aria-hidden="true"
                 style="background-image:radial-gradient(circle at 1px 1px, white 1px, transparent 0);background-size:28px 28px;"></div>
            {{-- Decorative blobs --}}
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-blue-500 opacity-40" aria-hidden="true"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 rounded-full bg-blue-700 opacity-40" aria-hidden="true"></div>

            <div class="relative px-8 py-12 sm:py-16 flex flex-col sm:flex-row items-center justify-between gap-8 text-center sm:text-left">
                <div>
                    <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest mb-3">Our Collections</p>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight">
                        Explore Our Collections
                    </h2>
                    <p class="mt-3 text-blue-100 text-sm sm:text-base max-w-md">
                        Find the right product for your needs across {{ $categories->count() }} carefully curated categories.
                    </p>
                </div>
                <a href="#categories"
                   class="shrink-0 inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-slate-50 font-semibold px-7 py-3.5 rounded-lg text-sm transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600">
                    Shop Now
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>


</div>{{-- /max-w-screen-xl --}}

{{-- ══════════════════════════════════════════════════════════════════
     H. FULL-WIDTH TRUST SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="bg-slate-50 border-t border-slate-200 py-16 lg:py-20" aria-labelledby="trust-heading">
    <div class="max-w-screen-xl mx-auto px-6 lg:px-12">
        <div class="text-center mb-12">
            <h2 id="trust-heading" class="text-3xl font-bold text-slate-900">Why Shop With Us?</h2>
            <p class="mt-2 text-slate-500 text-sm">We're committed to making your shopping experience the best it can be.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon'=>'🎯','title'=>'Curated Selection','body'=>'Every product is carefully reviewed and selected to meet our quality standards.'],
                ['icon'=>'🔍','title'=>'Easy to Discover','body'=>'Organised categories and subcategories make finding the right product simple.'],
                ['icon'=>'💬','title'=>'Responsive Support','body'=>'Have a question? Our team is ready to assist you at any time.'],
                ['icon'=>'🛡️','title'=>'Secure Experience','body'=>'Shop with confidence knowing your session and data are protected.'],
            ] as $t)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm text-center hover:shadow-md transition-shadow">
                <div class="text-4xl mb-4" aria-hidden="true">{{ $t['icon'] }}</div>
                <h3 class="font-semibold text-slate-900 text-sm mb-2">{{ $t['title'] }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $t['body'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
