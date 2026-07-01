@extends('layouts.admin')

@section('title', 'Add Livestream')
@section('page-title', 'Add Livestream')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Add Livestream</h1>
    <a href="{{ route('admin.livestreams.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.livestreams.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                placeholder="Sunday Morning Service">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Embed URL <span class="text-red-500">*</span></label>
            <input type="url" name="embed_url" value="{{ old('embed_url') }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('embed_url') border-red-400 @enderror"
                placeholder="https://www.youtube.com/embed/VIDEO_ID">
            <p class="text-xs text-gray-400 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Paste the YouTube embed URL (e.g., https://www.youtube.com/embed/VIDEO_ID), not the regular watch URL.
            </p>
            @error('embed_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Platform</label>
            <select name="platform" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="YouTube" {{ old('platform') === 'YouTube' ? 'selected' : '' }}>YouTube</option>
                <option value="Facebook" {{ old('platform') === 'Facebook' ? 'selected' : '' }}>Facebook</option>
                <option value="Zoom" {{ old('platform') === 'Zoom' ? 'selected' : '' }}>Zoom</option>
                <option value="Other" {{ old('platform') === 'Other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Scheduled At</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Short description of this service...">{{ old('description') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_live" value="1" {{ old('is_live') ? 'checked' : '' }}
                    class="w-4 h-4 text-red-600">
                <span class="text-sm font-semibold text-gray-700">Mark as Live Now</span>
            </label>
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Save Livestream
            </button>
            <a href="{{ route('admin.livestreams.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@endsection


