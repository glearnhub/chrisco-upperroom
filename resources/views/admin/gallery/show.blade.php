@extends('layouts.admin')

@section('title', $gallery->title ?? 'Gallery Photo')
@section('page-title', 'Gallery Photo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.gallery.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Back to Gallery</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.gallery.edit', $gallery) }}" class="btn-red px-4 py-2 text-sm">Edit</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <img src="{{ asset('storage/' . $gallery->image) }}" alt="{{ $gallery->title }}" class="w-full object-cover max-h-96">
        <div class="p-6 space-y-3">
            @if($gallery->title)
                <h2 class="text-xl font-bold">{{ $gallery->title }}</h2>
            @endif
            @if($gallery->caption)
                <p class="text-gray-600">{{ $gallery->caption }}</p>
            @endif
            <div class="text-sm text-gray-500 flex gap-4">
                <span>Category: {{ ucfirst($gallery->category) }}</span>
                <span>Status: {{ $gallery->is_published ? 'Published' : 'Hidden' }}</span>
                <span>Added: {{ $gallery->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
