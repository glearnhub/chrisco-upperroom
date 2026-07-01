@extends('layouts.admin')

@section('title', 'Edit Announcement')
@section('page-title', 'Edit Announcement')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Announcement</h1>
    <a href="{{ route('admin.announcements.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Body <span class="text-red-500">*</span></label>
            <textarea name="body" rows="6" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('body') border-red-400 @enderror">{{ old('body', $announcement->body) }}</textarea>
            @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Image</label>
            @if($announcement->image)
                <img src="{{ asset('storage/' . $announcement->image) }}" class="h-20 mb-2 rounded">
            @endif
            <input type="file" name="image" accept="image/*"
                class="w-full border border-gray-300 rounded-lg px-4 py-2">
            <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Expires At</label>
            <input type="datetime-local" name="expires_at"
                value="{{ old('expires_at', $announcement->expires_at ? \Carbon\Carbon::parse($announcement->expires_at)->format('Y-m-d\TH:i') : '') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-6">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1"
                    {{ old('is_published', $announcement->is_published) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600">
                <span class="text-sm font-semibold text-gray-700">Published</span>
            </label>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Update Announcement
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@endsection


