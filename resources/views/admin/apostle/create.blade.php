@extends('layouts.admin')

@section('title', 'Add Teaching')
@section('page-title', 'Add Teaching')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Add Teaching</h1>
    <a href="{{ route('admin.apostle.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.apostle.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                    placeholder="Teaching title">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">YouTube URL <span class="text-red-500">*</span></label>
                <input type="text" name="youtube_url" value="{{ old('youtube_url') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('youtube_url') border-red-400 @enderror"
                    placeholder="https://www.youtube.com/watch?v=...">
                <p class="text-xs text-gray-400 mt-1">Paste any YouTube link — watch, live, or short URL.</p>
                @error('youtube_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— No Category —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Thumbnail <span class="text-gray-400 font-normal text-xs">(optional — auto-pulled from YouTube if left blank)</span></label>
                <div class="flex items-start gap-4">
                    <div id="thumb-preview-wrap" class="hidden flex-shrink-0">
                        <img id="thumb-preview" src="#" alt="Preview" class="h-32 w-48 object-cover rounded-lg border border-gray-200">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="thumbnail" accept="image/*" id="thumb-input"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('thumbnail') border-red-400 @enderror"
                            onchange="previewThumb(this)">
                        @error('thumbnail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional description or notes...">{{ old('description') }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Save Teaching
            </button>
            <a href="{{ route('admin.apostle.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewThumb(input) {
    const wrap = document.getElementById('thumb-preview-wrap');
    const img = document.getElementById('thumb-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection
