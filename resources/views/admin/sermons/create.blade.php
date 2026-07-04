@extends('layouts.admin')

@section('title', 'Add Sermon')
@section('page-title', 'Add New Sermon')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Add New Sermon</h1>
    <a href="{{ route('admin.sermons.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.sermons.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                    placeholder="Sermon title">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Speaker <span class="text-red-500">*</span></label>
                <input type="text" name="speaker" value="{{ old('speaker') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('speaker') border-red-400 @enderror"
                    placeholder="Speaker name">
                @error('speaker')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Sermon Date <span class="text-red-500">*</span></label>
                <input type="date" name="sermon_date" value="{{ old('sermon_date') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sermon_date') border-red-400 @enderror">
                @error('sermon_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select Category —</option>
                    @foreach(\App\Models\Sermon::CATEGORIES as $slug => $label)
                        <option value="{{ $slug }}" {{ old('category') == $slug ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Scripture Reference</label>
                <input type="text" name="scripture" value="{{ old('scripture') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="e.g. John 3:16">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Audio URL</label>
                <input type="url" name="audio_url" value="{{ old('audio_url') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="https://...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Video URL</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="https://www.youtube.com/watch?v=...">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Thumbnail Image
                    <span class="text-gray-400 font-normal text-xs ml-1">(JPG, PNG or WebP · max 2MB · optional)</span>
                </label>
                <div class="flex items-start gap-4">
                    <div id="thumb-preview-wrap" class="hidden flex-shrink-0">
                        <img id="thumb-preview" src="#" alt="Preview" class="h-32 w-48 object-cover rounded-lg border border-gray-200">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="thumbnail" accept="image/*" id="thumb-input"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('thumbnail') border-red-400 @enderror"
                            onchange="previewThumb(this)">
                        <p class="text-xs text-gray-400 mt-1">Optional. Will show a placeholder cross icon if no thumbnail is uploaded.</p>
                        @error('thumbnail')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="5"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Sermon description or notes...">{{ old('description') }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Save Sermon
            </button>
            <a href="{{ route('admin.sermons.index') }}" class="btn-navy px-6 py-2">Cancel</a>
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


