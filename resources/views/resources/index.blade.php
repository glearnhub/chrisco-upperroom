@extends('layouts.app')

@section('title', 'Books & Articles — Chrisco Upper Room Fellowship')

@section('content')

<section style="background: #0a1f44; min-height: 220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-book text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Books & Articles</h1>
        <p class="text-gray-300">Download PDF resources to enrich your faith</p>
        @include('partials.resources-subnav')
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Books --}}
        @if($books->count())
        <div class="mb-14">
            <h2 class="text-2xl font-bold mb-6" style="color: #0a1f44; font-family: 'Playfair Display', serif;">
                <i class="fas fa-book mr-2" style="color: #c0392b;"></i>Books
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($books as $book)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition-shadow overflow-hidden">
                    @if($book->cover_image)
                        <div class="h-48 overflow-hidden">
                            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="h-48 flex items-center justify-center" style="background: linear-gradient(135deg, #0a1f44, #1a3a6e);">
                            <i class="fas fa-book text-5xl" style="color: #f0a500;"></i>
                        </div>
                    @endif
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 mb-1 leading-tight" style="font-family: 'Playfair Display', serif;">{{ $book->title }}</h3>
                        @if($book->author)
                            <p class="text-sm text-gray-500 mb-2"><i class="fas fa-user mr-1 text-xs"></i>{{ $book->author }}</p>
                        @endif
                        @if($book->description)
                            <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $book->description }}</p>
                        @endif
                        <a href="{{ route('resources.download', $book) }}"
                           class="block w-full text-center text-white text-sm font-semibold py-2 rounded-lg transition-colors"
                           style="background: #c0392b;">
                            <i class="fas fa-download mr-1"></i>Download PDF
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Articles --}}
        @if($articles->count())
        <div>
            <h2 class="text-2xl font-bold mb-6" style="color: #0a1f44; font-family: 'Playfair Display', serif;">
                <i class="fas fa-newspaper mr-2" style="color: #c0392b;"></i>Articles
            </h2>
            <div class="space-y-4">
                @foreach($articles as $article)
                <div class="bg-white rounded-xl shadow p-5 flex items-center space-x-5 hover:shadow-md transition-shadow">
                    @if($article->cover_image)
                        <img src="{{ Storage::url($article->cover_image) }}" alt="{{ $article->title }}"
                             class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-lg flex items-center justify-center flex-shrink-0" style="background: #0a1f44;">
                            <i class="fas fa-file-pdf text-xl" style="color: #f0a500;"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 leading-tight">{{ $article->title }}</h3>
                        @if($article->author)
                            <p class="text-sm text-gray-500 mt-0.5">by {{ $article->author }}</p>
                        @endif
                        @if($article->description)
                            <p class="text-sm text-gray-400 mt-1 line-clamp-1">{{ $article->description }}</p>
                        @endif
                    </div>
                    <a href="{{ route('resources.download', $article) }}"
                       class="flex-shrink-0 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors"
                       style="background: #0a1f44;">
                        <i class="fas fa-download mr-1"></i>PDF
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($books->isEmpty() && $articles->isEmpty())
            <div class="text-center py-20">
                <i class="fas fa-book text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No resources uploaded yet. Check back soon.</p>
            </div>
        @endif

    </div>
</section>

@endsection



