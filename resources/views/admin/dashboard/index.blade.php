@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-4">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        <x-admin.stat-card
            label="Total Categories"
            :value="$categoryCount"
            :subtext="$activeCategoryCount . ' active'"
            iconBg="bg-blue-100"
            iconColor="text-blue-600"
            iconPath="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
        />

        <x-admin.stat-card
            label="Subcategories"
            :value="$subcategoryCount"
            iconBg="bg-violet-100"
            iconColor="text-violet-600"
            iconPath="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
        />

        <x-admin.stat-card
            label="Total Products"
            :value="$productCount"
            iconBg="bg-blue-100"
            iconColor="text-blue-600"
            iconPath="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
        />

        <x-admin.stat-card
            label="Active Products"
            :value="$activeProductCount"
            iconBg="bg-emerald-100"
            iconColor="text-emerald-600"
            iconPath="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        />

        <x-admin.stat-card
            label="Inactive Products"
            :value="$inactiveProductCount"
            iconBg="bg-red-100"
            iconColor="text-red-600"
            iconPath="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
        />

        <x-admin.stat-card
            label="Total Orders"
            :value="$orderCount"
            iconBg="bg-blue-100"
            iconColor="text-blue-600"
            iconPath="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
        />

        <x-admin.stat-card
            label="Pending Orders"
            :value="$pendingOrderCount"
            iconBg="bg-orange-100"
            iconColor="text-orange-600"
            iconPath="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
        />

    </div>

    {{-- Quick Links --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <a
            href="{{ route('admin.categories.index') }}"
            class="flex items-center gap-3 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow"
        >
            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Manage Categories</span>
        </a>

        <a
            href="{{ route('admin.subcategories.index') }}"
            class="flex items-center gap-3 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow"
        >
            <div class="w-10 h-10 bg-violet-50 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Manage Subcategories</span>
        </a>

        <a
            href="{{ route('admin.products.index') }}"
            class="flex items-center gap-3 bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition-shadow"
        >
            <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-sm font-semibold text-slate-700">Manage Products</span>
        </a>
    </div>

    {{-- Welcome card --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-base font-semibold text-slate-800 mb-1">Welcome back, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-slate-500">Use the sidebar or quick links above to manage categories, subcategories, and products.</p>
    </div>

</div>
@endsection
