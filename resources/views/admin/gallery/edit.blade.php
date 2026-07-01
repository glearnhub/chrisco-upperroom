@extends('layouts.admin')

@section('title', 'Edit Photo')
@section('page-title', 'Edit Photo')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Photo</h1>
    <a href="{{ route('admin.gallery.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Current Photo</label>
            <img src="{{ Storage::url($gallery->image) }}" class="h-40 rounded-lg object-cover mb-2">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Replace Photo <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
            <input type="text" name="category" value="{{ old('category', $gallery->category) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="{{ old('title', $gallery->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Caption</label>
            <textarea name="caption" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('caption', $gallery->caption) }}</textarea>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $gallery->sort_order) }}" min="0"
                   class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-6 flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published" class="w-4 h-4"
                   {{ old('is_published', $gallery->is_published ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-semibold text-gray-700">Published</label>
        </div>

        <button type="submit" class="btn-red px-6 py-2 font-semibold">
            <i class="fas fa-save mr-2"></i>Save Changes
        </button>
    </form>
</div>

@endsection


