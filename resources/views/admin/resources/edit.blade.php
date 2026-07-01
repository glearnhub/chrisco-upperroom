@extends('layouts.admin')

@section('title', 'Edit Resource')
@section('page-title', 'Edit Resource')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Resource</h1>
    <a href="{{ route('admin.resources.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('admin.resources.update', $resource) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $resource->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('title') border-red-400 @enderror" required>
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Author</label>
            <input type="text" name="author" value="{{ old('author', $resource->author) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="book" {{ old('type', $resource->type) === 'book' ? 'selected' : '' }}>Book</option>
                <option value="article" {{ old('type', $resource->type) === 'article' ? 'selected' : '' }}>Article</option>
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('description', $resource->description) }}</textarea>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label>
            @if($resource->cover_image)
                <img src="{{ Storage::url($resource->cover_image) }}" class="h-24 rounded-lg object-cover mb-2">
                <p class="text-xs text-gray-400 mb-1">Upload a new image to replace.</p>
            @endif
            <input type="file" name="cover_image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">PDF File (leave blank to keep current)</label>
            <p class="text-xs text-gray-400 mb-1">Current: <span class="font-mono">{{ basename($resource->file_path) }}</span></p>
            <input type="file" name="file" accept=".pdf"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
        </div>

        <div class="mb-6 flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published"
                   class="w-4 h-4" {{ old('is_published', $resource->is_published ? '1' : '0') == '1' ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-semibold text-gray-700">Published</label>
        </div>

        <button type="submit" class="btn-red px-6 py-2 font-semibold">
            <i class="fas fa-save mr-2"></i>Save Changes
        </button>
    </form>
</div>

@endsection


