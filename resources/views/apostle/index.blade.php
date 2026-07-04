@extends('layouts.app')

@section('title', 'Apostle Das Teachings — Chrisco Upper Room Fellowship')

@section('content')

<section style="background: #0a1f44; min-height: 220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-video text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Apostle Das Teachings</h1>
        <p class="text-gray-300">Deep revelations from the Word of God</p>
        @include('partials.resources-subnav')
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex gap-8">

            {{-- Sidebar Category Navigation --}}
            <aside class="hidden md:block w-52 flex-shrink-0">
                <div class="sticky top-24 rounded-xl overflow-hidden shadow" style="background:#0a1f44;">
                    <div class="px-4 py-3 border-b border-white/10">
                        <p class="text-xs font-bold uppercase tracking-wider" style="color:#f0a500;">Categories</p>
                    </div>
                    <nav class="flex flex-col py-2">
                        <a href="{{ route('apostle.index') }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition-all {{ !$categoryId ? 'text-navy' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                           style="{{ !$categoryId ? 'background:#f0a500; color:#0a1f44;' : '' }}">
                            <i class="fas fa-th-large w-4 text-center opacity-60"></i>
                            All Teachings
                        </a>
                        @forelse($categories as $cat)
                        <a href="{{ route('apostle.index', ['category' => $cat->id]) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition-all {{ $categoryId == $cat->id ? 'text-navy' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                           style="{{ $categoryId == $cat->id ? 'background:#f0a500; color:#0a1f44;' : '' }}">
                            <i class="fas fa-bookmark w-4 text-center opacity-60"></i>
                            {{ $cat->name }}
                        </a>
                        @empty
                        <p class="px-4 py-3 text-xs text-gray-400">No categories yet.</p>
                        @endforelse
                    </nav>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile Category Pills --}}
                @if($categories->count())
                <div class="flex flex-wrap gap-2 mb-6 md:hidden">
                    <a href="{{ route('apostle.index') }}"
                       class="px-3 py-1 rounded-full text-xs font-semibold {{ !$categoryId ? '' : 'border border-gray-300 text-gray-600' }}"
                       style="{{ !$categoryId ? 'background:#f0a500; color:#0a1f44;' : '' }}">All</a>
                    @foreach($categories as $cat)
                    <a href="{{ route('apostle.index', ['category' => $cat->id]) }}"
                       class="px-3 py-1 rounded-full text-xs font-semibold {{ $categoryId == $cat->id ? '' : 'border border-gray-300 text-gray-600' }}"
                       style="{{ $categoryId == $cat->id ? 'background:#f0a500; color:#0a1f44;' : '' }}">{{ $cat->name }}</a>
                    @endforeach
                </div>
                @endif

                {{-- Teachings Grid --}}
                @if($teachings->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($teachings as $teaching)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
                                @if($teaching->thumbnail)
                                    <img src="{{ asset('storage/' . $teaching->thumbnail) }}" alt="{{ $teaching->title }}"
                                        class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    {{-- YouTube thumbnail fallback --}}
                                    @php
                                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/live\/)([a-zA-Z0-9_-]{11})/', $teaching->youtube_url, $ym);
                                        $ytThumb = isset($ym[1]) ? 'https://img.youtube.com/vi/' . $ym[1] . '/hqdefault.jpg' : null;
                                    @endphp
                                    @if($ytThumb)
                                        <div class="relative w-full h-44 overflow-hidden">
                                            <img src="{{ $ytThumb }}" alt="{{ $teaching->title }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                            <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.3);">
                                                <i class="fab fa-youtube text-5xl text-white opacity-80"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-44 flex items-center justify-center" style="background:#0a1f44;">
                                            <i class="fas fa-video text-white text-5xl opacity-30"></i>
                                        </div>
                                    @endif
                                @endif
                                <div class="p-5">
                                    @if($teaching->category)
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full text-white" style="background:#c0392b;">
                                            {{ $teaching->category->name }}
                                        </span>
                                    @endif
                                    <h3 class="text-base font-bold mt-2 mb-3 leading-snug" style="color:#0a1f44;">{{ $teaching->title }}</h3>
                                    <a href="{{ route('apostle.show', $teaching) }}"
                                        class="btn-red text-sm w-full text-center block">
                                        <i class="fas fa-play mr-1"></i>Watch
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10">
                        {{ $teachings->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <i class="fas fa-video text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No teachings found{{ $activeCategory ? ' in "' . $activeCategory->name . '"' : '' }}.</p>
                        @if($categoryId)
                            <a href="{{ route('apostle.index') }}" class="btn-navy mt-4 inline-block">View All</a>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

@endsection
