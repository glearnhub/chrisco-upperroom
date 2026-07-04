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
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror"
                placeholder="Announcement title">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
            <select name="category" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-400 @enderror">
                <option value="">-- Select Category --</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ old('category', $announcement->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Body <span class="text-red-500">*</span></label>
            <textarea name="body" rows="6" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('body') border-red-400 @enderror"
                placeholder="Announcement content...">{{ old('body', $announcement->body) }}</textarea>
            @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Image <span class="text-gray-400 font-normal">(Optional)</span></label>
            @if($announcement->image)
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ asset('storage/' . $announcement->image) }}" class="h-24 rounded-lg object-cover">
                    <label class="flex items-center space-x-1 text-xs text-red-600 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="w-3 h-3">
                        <span>Remove current image</span>
                    </label>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('image') border-red-400 @enderror"
                onchange="previewImage(this)">
            <img id="image-preview" src="#" alt="Preview" class="hidden mt-2 h-32 rounded-lg object-cover">
            @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Expires At <span class="text-gray-400 font-normal">(Optional)</span></label>
            <input type="datetime-local" name="expires_at"
                value="{{ old('expires_at', $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-400 mt-1">Leave empty for no expiry.</p>
        </div>

        <div class="mb-4 flex items-center flex-wrap gap-6">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600">
                <span class="text-sm font-semibold text-gray-700">Published</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}
                    class="w-4 h-4 text-yellow-500">
                <span class="text-sm font-semibold text-gray-700"><i class="fas fa-thumbtack mr-1 text-yellow-500"></i>Pin to Top</span>
            </label>
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="is_recurring" value="1" {{ old('is_recurring', $announcement->is_recurring) ? 'checked' : '' }}
                    class="w-4 h-4 text-purple-600">
                <span class="text-sm font-semibold text-gray-700"><i class="fas fa-sync-alt mr-1 text-purple-500"></i>Recurring</span>
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

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection
