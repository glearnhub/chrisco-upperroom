@extends('layouts.app')

@section('title', 'Text Teachings — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-10" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <i class="fas fa-book-open text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Text Teachings</h1>
        <p class="text-gray-300">Short teachings and devotionals from our pastoral team</p>
        <div class="mt-4 flex justify-center gap-4">
            <a href="{{ route('sermons.index') }}" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors">
                <i class="fas fa-microphone mr-1"></i>Sermons
            </a>
            <span class="text-yellow-400 text-sm font-semibold border-b-2 border-yellow-400 pb-0.5">
                <i class="fas fa-book-open mr-1"></i>Teachings
            </span>
            <a href="{{ route('resources.index') }}" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors">
                <i class="fas fa-file-pdf mr-1"></i>Books & Articles
            </a>
        </div>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        @if($teachings->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($teachings as $teaching)
                <a href="{{ route('teachings.show', $teaching) }}" class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden group">
                    @if($teaching->image)
                        <div class="h-52 overflow-hidden">
                            <img src="{{ Storage::url($teaching->image) }}" alt="{{ $teaching->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                    @else
                        <div class="h-52 flex items-center justify-center" style="background: #0a1f44;">
                            <i class="fas fa-book-open text-5xl" style="color: #f0a500;"></i>
                        </div>
                    @endif
                    <div class="p-6">
                        <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded-full mb-3 inline-block" style="background: #f0a500; color: #0a1f44;">
                            {{ $teaching->category ?: 'Teaching' }}
                        </span>
                        <h2 class="text-xl font-bold mb-2 group-hover:text-red-600 transition-colors" style="color: #0a1f44; font-family: 'Playfair Display', serif;">
                            {{ $teaching->title }}
                        </h2>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-3">{{ Str::limit(strip_tags($teaching->content), 150) }}</p>
                        <div class="flex items-center mt-4 pt-4 border-t border-gray-100">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2 flex-shrink-0" style="background: #c0392b;">
                                {{ strtoupper(substr($teaching->pastor_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">{{ $teaching->pastor_name }}</p>
                                <p class="text-xs text-gray-400">{{ $teaching->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $teachings->links() }}</div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No teachings published yet. Check back soon.</p>
            </div>
        @endif
    </div>
</section>

@endsection



