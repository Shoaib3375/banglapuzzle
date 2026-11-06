@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="py-4">

    <x-admin.page-header title="Categories" createRoute="admin.categories.create" createLabel="+ New Category" />

    {{-- Search bar --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-2 flex-wrap">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by name…"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 w-60"
            >
            <button type="submit" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-colors">
                Search
            </button>
            @if (request('search'))
                <a href="{{ route('admin.categories.index') }}" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Slug</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Subcategories</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Products</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500">{{ $category->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $category->slug }}</td>
                        <td class="px-4 py-3 text-center text-slate-700">{{ $category->subcategories_count }}</td>
                        <td class="px-4 py-3 text-center text-slate-700">{{ $category->products_count }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.categories.toggleActive', $category) }}">
                                @csrf
                                <button type="submit" class="cursor-pointer">
                                    <x-badge
                                        :variant="$category->is_active ? 'emerald' : 'red'"
                                        :text="$category->is_active ? 'Active' : 'Inactive'"
                                    />
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a
                                href="{{ route('admin.categories.edit', $category) }}"
                                class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors"
                            >
                                Edit
                            </a>
                            <form
                                method="POST"
                                action="{{ route('admin.categories.destroy', $category) }}"
                                class="inline-block"
                                onsubmit="return confirm('Delete category \'{{ addslashes($category->name) }}\'? This cannot be undone.')"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-lg bg-red-50 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors"
                                >
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <x-empty-state
                                title="No categories found"
                                description="Get started by creating your first category."
                                icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                actionLabel="+ New Category"
                                :actionUrl="route('admin.categories.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif

</div>
@endsection
