@extends('layouts.admin')

@section('title', 'Edit Teaching')
@section('page-title', 'Edit Teaching')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Teaching</h1>
    <a href="{{ route('admin.teachings.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-3xl">
    <form method="POST" action="{{ route('admin.teachings.update', $teaching) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $teaching->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('title') border-red-400 @enderror" required>
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Pastor's Name <span class="text-red-500">*</span></label>
            <input type="text" name="pastor_name" value="{{ old('pastor_name', $teaching->pastor_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('pastor_name') border-red-400 @enderror" required>
            @error('pastor_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ old('category', $teaching->category) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Featured Image</label>
            @if($teaching->image)
                <div class="mb-2">
                    <img src="{{ Storage::url($teaching->image) }}" class="h-24 rounded-lg object-cover">
                    <p class="text-xs text-gray-400 mt-1">Upload a new image to replace the current one.</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Teaching Content <span class="text-red-500">*</span></label>
            <textarea name="content" rows="16"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('content') border-red-400 @enderror"
                      required>{{ old('content', $teaching->content) }}</textarea>
            @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6 flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published"
                   class="w-4 h-4" {{ old('is_published', $teaching->is_published ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-semibold text-gray-700">Published</label>
        </div>

        <button type="submit" class="btn-red px-6 py-2 font-semibold">
            <i class="fas fa-save mr-2"></i>Save Changes
        </button>
    </form>
</div>

@endsection


