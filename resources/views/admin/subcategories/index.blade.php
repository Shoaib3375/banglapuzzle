@extends('layouts.admin')

@section('title', 'Subcategories')
@section('page-title', 'Subcategories')

@section('content')
<div class="py-4">
    {{-- Header row --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        {{-- Category filter --}}
        <form method="GET" action="{{ route('admin.subcategories.index') }}" class="flex gap-2 flex-wrap">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
            >
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @if (request('category_id'))
                <a href="{{ route('admin.subcategories.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 transition-colors">
                    Clear
                </a>
            @endif
        </form>

        <a
            href="{{ route('admin.subcategories.create') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow"
        >
            + New Subcategory
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Slug</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($subcategories as $subcategory)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ $subcategory->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $subcategory->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $subcategory->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $subcategory->slug }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.subcategories.toggleActive', $subcategory) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-semibold transition-colors
                                        {{ $subcategory->is_active
                                            ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                            : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                >
                                    {{ $subcategory->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a
                                href="{{ route('admin.subcategories.edit', $subcategory) }}"
                                class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors"
                            >
                                Edit
                            </a>
                            <form
                                method="POST"
                                action="{{ route('admin.subcategories.destroy', $subcategory) }}"
                                class="inline-block"
                                onsubmit="return confirm('Delete subcategory \'{{ addslashes($subcategory->name) }}\'?')"
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
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            No subcategories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($subcategories->hasPages())
        <div class="mt-4">
            {{ $subcategories->links() }}
        </div>
    @endif
</div>
@endsection
