@extends('layouts.admin')

@section('title', 'Gallery')
@section('page-title', 'Gallery')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Photo Gallery</h1>
    <a href="{{ route('admin.gallery.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-upload mr-2"></i>Upload Photos
    </a>
</div>

@if($items->count())
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
    @foreach($items as $item)
    <div class="bg-white rounded-xl shadow overflow-hidden group relative">
        <div class="aspect-square overflow-hidden">
            <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
        <div class="p-2">
            <p class="text-xs font-semibold text-gray-700 truncate">{{ $item->title ?: '—' }}</p>
            <span class="text-xs text-gray-400">{{ $item->category }}</span>
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full {{ $item->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $item->is_published ? 'Live' : 'Hidden' }}
            </span>
        </div>
        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('admin.gallery.edit', $item) }}"
               class="w-7 h-7 flex items-center justify-center rounded bg-yellow-400 text-white text-xs shadow">
                <i class="fas fa-edit"></i>
            </a>
            <form method="POST" action="{{ route('admin.gallery.destroy', $item) }}"
                  data-confirm="Delete this photo?" data-confirm-ok="Delete">
                @csrf @method('DELETE')
                <button type="submit"
                        class="w-7 h-7 flex items-center justify-center rounded bg-red-600 text-white text-xs shadow">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
<div>{{ $items->links() }}</div>
@else
    <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
        <i class="fas fa-images text-5xl mb-3"></i>
        <p>No photos yet. <a href="{{ route('admin.gallery.create') }}" class="text-red-600 underline">Upload some.</a></p>
    </div>
@endif

@endsection


