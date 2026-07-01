@extends('layouts.admin')

@section('title', 'Upload Resource')
@section('page-title', 'Upload Resource')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Upload PDF Resource</h1>
    <a href="{{ route('admin.resources.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('admin.resources.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 @error('title') border-red-400 @enderror"
                   placeholder="Book or article title" required>
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Author</label>
            <input type="text" name="author" value="{{ old('author') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   placeholder="Author name">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="book" {{ old('type') === 'book' ? 'selected' : '' }}>Book</option>
                <option value="article" {{ old('type') === 'article' ? 'selected' : '' }}>Article</option>
            </select>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                      placeholder="Brief description...">{{ old('description') }}</textarea>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image (optional)</label>
            <input type="file" name="cover_image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm @error('cover_image') border-red-400 @enderror">
            @error('cover_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">PDF File <span class="text-red-500">*</span></label>
            <input type="file" name="file" accept=".pdf"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm @error('file') border-red-400 @enderror" required>
            <p class="text-xs text-gray-400 mt-1">Max 20MB. PDF only.</p>
            @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6 flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published"
                   class="w-4 h-4" {{ old('is_published', '1') == '1' ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-semibold text-gray-700">Publish immediately</label>
        </div>

        <button type="submit" class="btn-red px-6 py-2 font-semibold">
            <i class="fas fa-upload mr-2"></i>Upload Resource
        </button>
    </form>
</div>

@endsection


