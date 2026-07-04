@extends('layouts.app')

@section('title', 'Sermons & Messages — Chrisco Upper Room Fellowship')

@section('content')

<section style="background: #0a1f44; min-height: 180px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-bible text-4xl sm:text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2">Sermons &amp; Messages</h1>
        <p class="text-gray-300">Be empowered by the Word of God</p>
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
                        <a href="{{ route('sermons.index') }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition-all {{ !$category || $category === 'all' ? 'text-navy' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                           style="{{ !$category || $category === 'all' ? 'background:#f0a500; color:#0a1f44;' : '' }}">
                            <i class="fas fa-th-large w-4 text-center opacity-60"></i>
                            All Sermons
                        </a>
                        @foreach($categories as $slug => $label)
                        <a href="{{ route('sermons.index', ['category' => $slug]) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition-all {{ $category === $slug ? 'text-navy' : 'text-gray-300 hover:text-white hover:bg-white/5' }}"
                           style="{{ $category === $slug ? 'background:#f0a500; color:#0a1f44;' : '' }}">
                            <i class="fas fa-bookmark w-4 text-center opacity-60"></i>
                            {{ $label }}
                        </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">

                {{-- Mobile Category Pills --}}
                <div class="flex flex-wrap gap-2 mb-6 md:hidden">
                    <a href="{{ route('sermons.index') }}"
                       class="px-4 py-2 rounded-full text-sm font-semibold {{ !$category || $category === 'all' ? '' : 'border border-gray-300 text-gray-600' }}"
                       style="{{ !$category || $category === 'all' ? 'background:#f0a500; color:#0a1f44;' : '' }}">All</a>
                    @foreach($categories as $slug => $label)
                    <a href="{{ route('sermons.index', ['category' => $slug]) }}"
                       class="px-4 py-2 rounded-full text-sm font-semibold {{ $category === $slug ? '' : 'border border-gray-300 text-gray-600' }}"
                       style="{{ $category === $slug ? 'background:#f0a500; color:#0a1f44;' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>

                {{-- Sermons Grid --}}
                @if($sermons->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($sermons as $sermon)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
                                @php $thumb = $sermon->thumbnail ? asset('storage/' . $sermon->thumbnail) : $sermon->youtube_thumbnail; @endphp
                                @if($thumb)
                                    <img src="{{ $thumb }}" alt="{{ $sermon->title }}"
                                        class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-44 flex items-center justify-center" style="background: #0a1f44;">
                                        <i class="fas fa-cross text-white text-5xl opacity-30"></i>
                                    </div>
                                @endif
                                <div class="p-5">
                                    @if($sermon->category)
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full text-white" style="background: #c0392b;">
                                            {{ \App\Models\Sermon::CATEGORIES[$sermon->category] ?? $sermon->category }}
                                        </span>
                                    @endif
                                    <h3 class="text-lg font-bold mt-2 mb-1 leading-snug" style="color: #0a1f44;">{{ $sermon->title }}</h3>
                                    <p class="text-gray-500 text-sm mb-1">
                                        <i class="fas fa-church mr-1 text-gray-400"></i>Chrisco Upper Room
                                    </p>
                                    <p class="text-gray-400 text-xs mb-1">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ \Carbon\Carbon::parse($sermon->sermon_date)->format('M d, Y') }}
                                    </p>
                                    @if($sermon->scripture)
                                        <p class="text-gray-400 text-xs mb-3 italic">
                                            <i class="fas fa-book mr-1"></i>{{ $sermon->scripture }}
                                        </p>
                                    @endif
                                    <a href="{{ route('sermons.show', $sermon) }}"
                                        class="btn-red text-sm w-full text-center block mt-3">
                                        <i class="fas fa-play mr-1"></i>Watch
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10">
                        {{ $sermons->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <i class="fas fa-bible text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">No sermons found in this category.</p>
                        <a href="{{ route('sermons.index') }}" class="btn-navy mt-4 inline-block">View All Sermons</a>
                    </div>
                @endif

            </div>{{-- end main --}}
        </div>{{-- end flex --}}
    </div>
</section>

@endsection



