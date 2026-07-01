@extends('layouts.app')

@section('title', 'Sermons & Messages — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-2">Sermons &amp; Messages</h1>
        <p class="text-gray-300">Be empowered by the Word of God</p>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Search Bar --}}
        <form method="GET" action="{{ route('sermons.index') }}" class="mb-8 flex gap-2 max-w-lg mx-auto">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search sermons, speakers, series..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="btn-red px-6 py-2 rounded-lg">
                <i class="fas fa-search mr-1"></i>Search
            </button>
        </form>

        {{-- Sermons Grid --}}
        @if($sermons->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($sermons as $sermon)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
                        @if($sermon->thumbnail)
                            <img src="{{ asset('storage/' . $sermon->thumbnail) }}" alt="{{ $sermon->title }}"
                                class="w-full h-44 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-44 flex items-center justify-center" style="background: #0a1f44;">
                                <i class="fas fa-cross text-white text-5xl opacity-30"></i>
                            </div>
                        @endif
                        <div class="p-5">
                            @if($sermon->series)
                                <span class="text-xs font-semibold px-2 py-1 rounded-full text-white" style="background: #c0392b;">
                                    {{ $sermon->series }}
                                </span>
                            @endif
                            <h3 class="text-lg font-bold mt-2 mb-1 leading-snug" style="color: #0a1f44;">{{ $sermon->title }}</h3>
                            <p class="text-gray-500 text-sm mb-1">
                                <i class="fas fa-user mr-1 text-gray-400"></i>{{ $sermon->speaker }}
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
                                <i class="fas fa-play mr-1"></i>Listen / Watch
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $sermons->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-bible text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No sermons found{{ request('q') ? ' for "' . request('q') . '"' : '' }}.</p>
                @if(request('q'))
                    <a href="{{ route('sermons.index') }}" class="btn-navy mt-4 inline-block">Clear Search</a>
                @endif
            </div>
        @endif
    </div>
</section>

@endsection



