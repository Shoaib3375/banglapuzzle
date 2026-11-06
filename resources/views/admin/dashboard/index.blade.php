@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-4">

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        {{-- Total Categories --}}
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Categories</p>
                <p class="text-3xl font-bold text-indigo-600 mt-0.5">{{ $categoryCount }}</p>
                <p class="text-xs text-green-600 mt-0.5">{{ $activeCategoryCount }} active</p>
            </div>
        </div>

        {{-- Total Subcategories --}}
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Subcategories</p>
                <p class="text-3xl font-bold text-purple-600 mt-0.5">{{ $subcategoryCount }}</p>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Products</p>
                <p class="text-3xl font-bold text-blue-600 mt-0.5">{{ $productCount }}</p>
            </div>
        </div>

        {{-- Active Products --}}
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Active Products</p>
                <p class="text-3xl font-bold text-green-600 mt-0.5">{{ $activeProductCount }}</p>
            </div>
        </div>

        {{-- Inactive Products --}}
        <div class="bg-white rounded-xl shadow p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500">Inactive Products</p>
                <p class="text-3xl font-bold text-red-600 mt-0.5">{{ $inactiveProductCount }}</p>
            </div>
        </div>

    </div>

    {{-- Welcome card --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-gray-500">Use the sidebar to manage categories, subcategories, and products.</p>
    </div>

</div>
@endsection
