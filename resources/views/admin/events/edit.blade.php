@extends('layouts.admin')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit Event</h1>
    <a href="{{ route('admin.events.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-400 @enderror">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date &amp; Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="start_datetime"
                    value="{{ old('start_datetime', \Carbon\Carbon::parse($event->start_datetime)->format('Y-m-d\TH:i')) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_datetime') border-red-400 @enderror">
                @error('start_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Date &amp; Time</label>
                <input type="datetime-local" name="end_datetime"
                    value="{{ old('end_datetime', $event->end_datetime ? \Carbon\Carbon::parse($event->end_datetime)->format('Y-m-d\TH:i') : '') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Location <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('location') border-red-400 @enderror">
                @error('location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ old('status', $event->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="past" {{ old('status', $event->status) === 'past' ? 'selected' : '' }}>Past</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Event Image</label>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <img id="event-img-preview"
                            src="{{ $event->image ? asset('storage/' . $event->image) : '' }}"
                            alt="Event image"
                            class="h-36 w-56 object-cover rounded-lg border border-gray-200 {{ $event->image ? '' : 'hidden' }}">
                        @if(!$event->image)
                            <div id="event-img-placeholder" class="h-36 w-56 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm">
                                <span><i class="fas fa-image mr-1"></i>No image</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" accept="image/*"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('image') border-red-400 @enderror"
                            onchange="previewEventImg(this)">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep current image. JPG, PNG or WebP · max 2MB.</p>
                        @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer mt-5">
                    <input type="checkbox" name="registration_required" value="1"
                        {{ old('registration_required', $event->registration_required) ? 'checked' : '' }}
                        class="w-4 h-4 text-red-600">
                    <span class="text-sm font-semibold text-gray-700">Registration Required</span>
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="5"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $event->description) }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="btn-red px-6 py-2">
                <i class="fas fa-save mr-2"></i>Update Event
            </button>
            <a href="{{ route('admin.events.index') }}" class="btn-navy px-6 py-2">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewEventImg(input) {
    const img = document.getElementById('event-img-preview');
    const placeholder = document.getElementById('event-img-placeholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush

@endsection


