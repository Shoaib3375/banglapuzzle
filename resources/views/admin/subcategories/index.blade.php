@extends('layouts.admin')

@section('title', 'Subcategories')
@section('page-title', 'Subcategories')

@section('content')
<div class="py-4">

    <x-admin.page-header title="Subcategories" createRoute="admin.subcategories.create" createLabel="+ New Subcategory" />

    {{-- Category filter --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('admin.subcategories.index') }}" class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <select
                name="category_id"
                onchange="this.form.submit()"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
                <option value="">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @if (request('category_id'))
                <a href="{{ route('admin.subcategories.index') }}" class="w-full sm:w-auto text-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-200 transition-colors">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Category</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Slug</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($subcategories as $subcategory)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500">{{ $subcategory->id }}</td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $subcategory->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $subcategory->category->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $subcategory->slug }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.subcategories.toggleActive', $subcategory) }}">
                                @csrf
                                <button type="submit" class="cursor-pointer">
                                    <x-badge
                                        :variant="$subcategory->is_active ? 'emerald' : 'red'"
                                        :text="$subcategory->is_active ? 'Active' : 'Inactive'"
                                    />
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a
                                href="{{ route('admin.subcategories.edit', $subcategory) }}"
                                class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors"
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
                        <td colspan="6" class="p-0">
                            <x-empty-state
                                title="No subcategories found"
                                description="Get started by creating your first subcategory."
                                icon="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                actionLabel="+ New Subcategory"
                                :actionUrl="route('admin.subcategories.create')"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($subcategories->hasPages())
        <div class="mt-4">
            {{ $subcategories->links() }}
        </div>
    @endif

</div>
@endsection
