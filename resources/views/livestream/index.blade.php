@extends('layouts.app')

@section('title', 'Live Service — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <i class="fas fa-broadcast-tower text-4xl mb-3 text-yellow-400"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Live Service</h1>
        <p class="text-gray-300">Watch our services from anywhere in the world</p>
    </div>
</section>

<section class="py-10 bg-gray-900">
    <div class="max-w-5xl mx-auto px-4">

        @if(isset($livestream) && $livestream)
            {{-- Live Badge --}}
            @if($livestream->is_live)
                <div class="flex items-center justify-center mb-4 space-x-3">
                    <span class="relative flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600"></span>
                    </span>
                    <span class="text-red-500 font-bold text-xl uppercase tracking-widest">Live Now</span>
                </div>
            @endif

            <h2 class="text-2xl font-bold text-white text-center mb-2">{{ $livestream->title }}</h2>
            @if($livestream->description)
                <p class="text-gray-400 text-center mb-6">{{ $livestream->description }}</p>
            @endif

            {{-- Platform Badge --}}
            @if($livestream->platform)
                <div class="text-center mb-4">
                    <span class="bg-red-700 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">
                        <i class="fab fa-{{ strtolower($livestream->platform) === 'youtube' ? 'youtube' : 'video' }} mr-1"></i>
                        {{ $livestream->platform }}
                    </span>
                </div>
            @endif

            {{-- Video Embed --}}
            <div class="relative w-full rounded-xl overflow-hidden shadow-2xl" style="padding-top: 56.25%;">
                <iframe class="absolute inset-0 w-full h-full"
                    src="{{ $livestream->embed_url }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                </iframe>
            </div>

            @if($livestream->scheduled_at && !$livestream->is_live)
                <div class="mt-4 text-center text-gray-400 text-sm">
                    <i class="fas fa-clock mr-1 text-yellow-400"></i>
                    Scheduled for: {{ \Carbon\Carbon::parse($livestream->scheduled_at)->format('F d, Y \a\t g:i A') }}
                </div>
            @endif

        @else
            {{-- No Active Livestream --}}
            <div class="text-center py-20">
                <i class="fas fa-tv text-7xl text-gray-600 mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-300 mb-3">No Live Service at the Moment</h2>
                <p class="text-gray-500 mb-2">Check back on <strong class="text-yellow-400">Sunday at 9:00 AM</strong> for our live service.</p>
                <p class="text-gray-600 text-sm">You can also watch our past sermons while you wait.</p>
                <a href="{{ route('sermons.index') }}" class="btn-red mt-6 inline-block">
                    <i class="fas fa-play mr-2"></i>Watch Past Sermons
                </a>
            </div>
        @endif
    </div>
</section>

{{-- Service Schedule --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-center mb-8" style="color: #0a1f44;">Service Schedule</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color: #c0392b;">
                <i class="fas fa-sun text-3xl mb-3" style="color: #f0a500;"></i>
                <h3 class="font-bold" style="color: #0a1f44;">Sunday Service</h3>
                <p class="text-gray-500 text-sm">Main Worship</p>
                <p class="font-bold text-xl mt-2" style="color: #c0392b;">9:00 AM</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color: #0a1f44;">
                <i class="fas fa-book-open text-3xl mb-3" style="color: #0a1f44;"></i>
                <h3 class="font-bold" style="color: #0a1f44;">Wednesday Bible Study</h3>
                <p class="text-gray-500 text-sm">Midweek Teaching</p>
                <p class="font-bold text-xl mt-2" style="color: #c0392b;">6:00 PM</p>
            </div>
            <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color: #f0a500;">
                <i class="fas fa-praying-hands text-3xl mb-3" style="color: #f0a500;"></i>
                <h3 class="font-bold" style="color: #0a1f44;">Friday Night Prayer</h3>
                <p class="text-gray-500 text-sm">Corporate Prayer</p>
                <p class="font-bold text-xl mt-2" style="color: #c0392b;">7:00 PM</p>
            </div>
        </div>
    </div>
</section>

@endsection



