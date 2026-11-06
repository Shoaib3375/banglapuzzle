@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="py-4">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Categories</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $categoryCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Subcategories</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $subcategoryCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <p class="text-sm text-gray-500">Products</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $productCount }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-gray-500">Use the sidebar to manage categories, subcategories, and products.</p>
    </div>
</div>
@endsection
