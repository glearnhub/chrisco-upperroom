@extends('layouts.admin')

@section('title', 'Add Announcement')
@section('page-title', 'Add Announcement')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Add Announcement</h1>
    <a href="{{ route('admin.announcements.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.announcements.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                placeholder="Announcement title">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Body <span class="text-red-500">*</span></label>
            <textarea name="body" rows="6" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('body') border-red-400 @enderror"
                placeholder="Announcement content...">{{ old('body') }}</textarea>
            @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Image (Optional)</label>
            <input type="file" name="image" accept="image/*"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('image') border-red-400 @enderror">
            @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Expires At</label>
            <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-400 mt-1">Leave empty for no expiry.</p>
        </div>

        <div class="mb-6">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600">
                <span class="text-sm font-semibold text-gray-700">Publish Immediately</span>
            </label>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Save Announcement
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@endsection


