@extends('layouts.admin')

@section('title', 'Edit Sermon')
@section('page-title', 'Edit Sermon')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Sermon</h1>
    <a href="{{ route('admin.sermons.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.sermons.update', $sermon) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $sermon->title) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Speaker <span class="text-red-500">*</span></label>
                <input type="text" name="speaker" value="{{ old('speaker', $sermon->speaker) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('speaker') border-red-400 @enderror">
                @error('speaker')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sermon Date <span class="text-red-500">*</span></label>
                <input type="date" name="sermon_date" value="{{ old('sermon_date', \Carbon\Carbon::parse($sermon->sermon_date)->format('Y-m-d')) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sermon_date') border-red-400 @enderror">
                @error('sermon_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Series</label>
                <input type="text" name="series" value="{{ old('series', $sermon->series) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Scripture Reference</label>
                <input type="text" name="scripture" value="{{ old('scripture', $sermon->scripture) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Audio URL</label>
                <input type="url" name="audio_url" value="{{ old('audio_url', $sermon->audio_url) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL</label>
                <input type="url" name="video_url" value="{{ old('video_url', $sermon->video_url) }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail Image</label>
                @if($sermon->thumbnail)
                    <img src="{{ asset('storage/' . $sermon->thumbnail) }}" class="h-20 mb-2 rounded">
                @endif
                <input type="file" name="thumbnail" accept="image/*"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft" {{ old('status', $sermon->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $sermon->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="5"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $sermon->description) }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Update Sermon
            </button>
            <a href="{{ route('admin.sermons.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@endsection


