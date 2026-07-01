@extends('layouts.app')

@section('title', 'Chrisco Upper Room Fellowship — Where God Dwells')

@section('content')

{{-- HERO SECTION --}}
<section class="relative min-h-screen flex items-center justify-center text-white"
    style="background: linear-gradient(135deg, #0a1f44 0%, #1a3a6b 50%, #0a1f44 100%);">

    <div class="absolute inset-0 opacity-20"
        style="background-image: url('https://images.unsplash.com/photo-1478147427282-58a87a120781?w=1400'); background-size: cover; background-position: center;">
    </div>
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">

        <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight" style="font-family: 'Playfair Display', serif;">
            Welcome to Chrisco Upper Room Fellowship
        </h1>
        <p class="text-xl md:text-2xl mb-3 italic text-yellow-400">"Where God Dwells"</p>
        <p class="text-lg text-gray-300 mb-8">
            <i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>Nairobi, Kenya
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('events.index') }}" class="btn-red text-lg font-semibold px-8 py-3 transition hover:scale-105">
                <i class="fas fa-church mr-2"></i>Join Us Sunday
            </a>
            <a href="{{ route('livestream') }}" class="border-2 border-white text-white text-lg font-semibold px-8 py-3 rounded hover:bg-white hover:text-gray-900 transition">
                <i class="fas fa-play-circle mr-2"></i>Watch Livestream
            </a>
        </div>
    </div>
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-white text-2xl opacity-60"></i>
    </div>
</section>

{{-- LIVE NOW BANNER --}}
@if(isset($livestream) && $livestream && $livestream->is_live)
    <div style="background: #c0392b;" class="py-3">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-center space-x-4">
            <span class="flex items-center space-x-2">
                <span class="w-3 h-3 bg-white rounded-full animate-ping inline-block"></span>
                <span class="w-3 h-3 bg-white rounded-full absolute inline-block"></span>
            </span>
            <span class="text-white font-bold text-lg uppercase tracking-widest">LIVE NOW</span>
            <a href="{{ route('livestream') }}" class="bg-white text-red-700 font-semibold text-sm px-4 py-1 rounded hover:bg-gray-100 transition">
                Watch Now <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
@endif

{{-- SERVICE TIMES --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Service Times</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-8 rounded-xl shadow-md border-t-4" style="border-color: #c0392b;">
                <i class="fas fa-sun text-4xl mb-4" style="color: #f0a500;"></i>
                <h3 class="text-xl font-bold mb-1" style="color: #0a1f44;">Sunday Service</h3>
                <p class="text-gray-500 text-sm mb-2">Main Worship Service</p>
                <p class="text-2xl font-bold" style="color: #c0392b;">9:00 AM</p>
            </div>
            <div class="text-center p-8 rounded-xl shadow-md border-t-4" style="border-color: #0a1f44;">
                <i class="fas fa-book-open text-4xl mb-4" style="color: #0a1f44;"></i>
                <h3 class="text-xl font-bold mb-1" style="color: #0a1f44;">Wednesday Bible Study</h3>
                <p class="text-gray-500 text-sm mb-2">Midweek Teaching</p>
                <p class="text-2xl font-bold" style="color: #c0392b;">6:00 PM</p>
            </div>
            <div class="text-center p-8 rounded-xl shadow-md border-t-4" style="border-color: #f0a500;">
                <i class="fas fa-praying-hands text-4xl mb-4" style="color: #f0a500;"></i>
                <h3 class="text-xl font-bold mb-1" style="color: #0a1f44;">Friday Night Prayer</h3>
                <p class="text-gray-500 text-sm mb-2">Corporate Prayer</p>
                <p class="text-2xl font-bold" style="color: #c0392b;">7:00 PM</p>
            </div>
        </div>
    </div>
</section>

{{-- LATEST SERMONS --}}
<section class="py-16" style="background: #f8fafc;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Latest Sermons</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
            <p class="text-gray-500 mt-3">Be transformed by the Word of God</p>
        </div>
        @if(isset($sermons) && $sermons->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($sermons as $sermon)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
                        @if($sermon->thumbnail)
                            <img src="{{ asset('storage/' . $sermon->thumbnail) }}" alt="{{ $sermon->title }}" class="w-full h-44 object-cover">
                        @else
                            <div class="w-full h-44 flex items-center justify-center" style="background: #0a1f44;">
                                <i class="fas fa-cross text-white text-5xl opacity-30"></i>
                            </div>
                        @endif
                        <div class="p-5">
                            @if($sermon->series)
                                <span class="text-xs font-semibold px-2 py-1 rounded-full text-white" style="background: #c0392b;">{{ $sermon->series }}</span>
                            @endif
                            <h3 class="text-lg font-bold mt-2 mb-1" style="color: #0a1f44;">{{ $sermon->title }}</h3>
                            <p class="text-gray-500 text-sm mb-1"><i class="fas fa-user mr-1"></i>{{ $sermon->speaker }}</p>
                            <p class="text-gray-400 text-xs mb-4"><i class="fas fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($sermon->sermon_date)->format('M d, Y') }}</p>
                            <a href="{{ route('sermons.show', $sermon) }}" class="btn-red text-sm w-full text-center block">
                                <i class="fas fa-play mr-1"></i>Listen / Watch
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('sermons.index') }}" class="btn-navy px-8 py-3">View All Sermons</a>
            </div>
        @else
            <p class="text-center text-gray-500">No sermons available at the moment. Check back soon!</p>
        @endif
    </div>
</section>

{{-- UPCOMING EVENTS --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Upcoming Events</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        @if(isset($events) && $events->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($events as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-xl transition">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-1 rounded-full">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d') }}
                                </span>
                                <span class="text-gray-400 text-xs">
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold mb-1" style="color: #0a1f44;">{{ $event->title }}</h3>
                            <p class="text-gray-500 text-sm mb-4">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>{{ $event->location }}
                            </p>
                            <a href="{{ route('events.show', $event) }}" class="btn-red text-sm w-full text-center block">
                                Register / Learn More
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('events.index') }}" class="btn-navy px-8 py-3">View All Events</a>
            </div>
        @else
            <p class="text-center text-gray-500">No upcoming events at this time.</p>
        @endif
    </div>
</section>

{{-- LATEST ANNOUNCEMENTS --}}
@if(isset($announcements) && $announcements->count())
<section class="py-12" style="background: #f8fafc;">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Announcements</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <div class="bg-white rounded-lg shadow p-5 flex items-start space-x-4 border-l-4" style="border-color: #0a1f44;">
                    <div class="mt-1">
                        <i class="fas fa-bullhorn text-xl" style="color: #c0392b;"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 mb-1">{{ $announcement->title }}</h4>
                        <p class="text-gray-600 text-sm">{{ Str::limit($announcement->body, 150) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- GIVE / DONATE CTA --}}
<section class="py-16 text-white text-center" style="background: #0a1f44;">
    <div class="max-w-3xl mx-auto px-4">
        <i class="fas fa-hand-holding-heart text-5xl mb-4" style="color: #f0a500;"></i>
        <h2 class="text-3xl font-bold mb-3" style="font-family: 'Playfair Display', serif;">Support the Ministry</h2>
        <p class="text-gray-300 mb-2">Your giving helps us spread the Gospel and serve the community.</p>
        <p class="text-lg text-yellow-400 mb-6 font-semibold">M-Pesa Paybill: <span class="text-white">566422</span></p>
        <a href="{{ route('give') }}" class="btn-red text-lg px-10 py-3 font-semibold hover:scale-105 transition">
            <i class="fas fa-heart mr-2"></i>Give Online
        </a>
    </div>
</section>

{{-- PRAYER CTA --}}
<section class="py-12 bg-white text-center">
    <div class="max-w-2xl mx-auto px-4">
        <i class="fas fa-praying-hands text-4xl mb-3" style="color: #0a1f44;"></i>
        <h2 class="text-2xl font-bold mb-2" style="color: #0a1f44;">We Pray With You</h2>
        <p class="text-gray-500 mb-6">Share your prayer request and our team will intercede on your behalf.</p>
        <a href="{{ route('prayer.index') }}" class="btn-navy px-8 py-3 font-semibold">
            <i class="fas fa-paper-plane mr-2"></i>Submit Prayer Request
        </a>
    </div>
</section>

@endsection



