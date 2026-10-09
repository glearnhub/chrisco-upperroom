@extends('layouts.admin')

@section('title', 'Teaching Categories')
@section('page-title', 'Teaching Categories')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Apostle Das — Categories</h1>
    <a href="{{ route('admin.apostle.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back to Teachings
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Add Category --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-bold mb-4" style="color:#0a1f44;">Add New Category</h2>
        <form method="POST" action="{{ route('admin.apostle.categories.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                    placeholder="e.g. Prayer & Intercession">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0">
                <p class="text-xs text-gray-400 mt-1">Lower numbers appear first.</p>
            </div>
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-plus mr-2"></i>Add Category
            </button>
        </form>
    </div>

    {{-- Categories List --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b" style="background:#f8fafc;">
            <h2 class="text-lg font-bold" style="color:#0a1f44;">Existing Categories</h2>
        </div>
        @forelse($categories as $cat)
        <div class="px-6 py-4 border-b last:border-0">
            <form method="POST" action="{{ route('admin.apostle.categories.update', $cat) }}" class="flex items-center gap-3">
                @csrf @method('PATCH')
                <input type="text" name="name" value="{{ $cat->name }}" required
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" name="sort_order" value="{{ $cat->sort_order }}" min="0"
                    class="w-16 border border-gray-300 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none">
                <button type="submit" class="btn-navy px-3 py-1.5 text-xs">
                    <i class="fas fa-save"></i>
                </button>
            </form>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs text-gray-400">{{ $cat->teachings_count }} teaching{{ $cat->teachings_count !== 1 ? 's' : '' }}</span>
                <form method="POST" action="{{ route('admin.apostle.categories.destroy', $cat) }}"
                    data-confirm="Delete this category? Teachings will become uncategorised." data-confirm-ok="Delete">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-gray-400">
            <i class="fas fa-tags text-3xl mb-2 block"></i>No categories yet.
        </div>
        @endforelse
    </div>

</div>

@endsection
