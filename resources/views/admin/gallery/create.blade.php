@extends('layouts.admin')

@section('title', 'Upload Photos')
@section('page-title', 'Upload Photos')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Upload Photos</h1>
    <a href="{{ route('admin.gallery.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8 max-w-2xl">
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Photos <span class="text-red-500">*</span>
                <span class="text-gray-400 font-normal">(you can select multiple)</span>
            </label>
            <input type="file" name="images[]" accept="image/*" multiple required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm {{ $errors->hasAny(['images','images.*']) ? 'border-red-400' : '' }}"
                   id="photo-input" onchange="previewPhotos(this)">
            <p class="text-xs text-gray-400 mt-1">Max 5MB per photo. JPG, PNG, WEBP.</p>
            @error('images')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            @error('images.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Preview --}}
        <div id="photo-preview" class="grid grid-cols-4 gap-2 mb-5 hidden"></div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
            <input type="text" name="category" value="{{ old('category', 'General') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   placeholder="e.g. Worship, Events, Youth, Outreach" required>
            <p class="text-xs text-gray-400 mt-1">Used for filtering on the public gallery page.</p>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-gray-400 font-normal">(optional — applies to all selected photos)</span></label>
            <input type="text" name="title" value="{{ old('title') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                   placeholder="e.g. Sunday Service — June 2025">
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Caption <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="caption" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                      placeholder="Short description shown on hover...">{{ old('caption') }}</textarea>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Display Order</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                   class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-6 flex items-center space-x-2">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" id="is_published" class="w-4 h-4"
                   {{ old('is_published', '1') == '1' ? 'checked' : '' }}>
            <label for="is_published" class="text-sm font-semibold text-gray-700">Publish immediately</label>
        </div>

        <button type="submit" class="btn-red px-6 py-2 font-semibold">
            <i class="fas fa-upload mr-2"></i>Upload Photos
        </button>
    </form>
</div>

@push('scripts')
<script>
    function previewPhotos(input) {
        const preview = document.getElementById('photo-preview');
        preview.innerHTML = '';
        if (!input.files.length) { preview.classList.add('hidden'); return; }
        preview.classList.remove('hidden');
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'aspect-square rounded-lg overflow-hidden bg-gray-100';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush

@endsection


