@extends('layouts.app')

@section('title', 'Chrisco Upper Room Fellowship — Where God Dwells')

@section('content')

{{-- CANCELLED EVENTS POPUP --}}
@if(isset($cancelledEvents) && $cancelledEvents->isNotEmpty())
<div id="cancelled-events-modal"
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
    style="background: rgba(10,31,68,0.75); backdrop-filter: blur(3px);">

    <div class="relative rounded-2xl shadow-2xl w-full max-w-md overflow-hidden" style="background: #fffbf0;">
        {{-- Gold top bar --}}
        <div class="flex items-center gap-3 px-6 py-4" style="background: #f0a500;">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: #0a1f44;">
                <i class="fas fa-calendar-xmark text-sm" style="color: #f0a500;"></i>
            </div>
            <div>
                <h2 class="font-bold text-lg leading-tight" style="color: #0a1f44;">Event Notice</h2>
                <p class="text-sm font-medium" style="color: #0a1f44; opacity: 0.75;">Please read before attending</p>
            </div>
            <button onclick="closeCancelledModal()" class="ml-auto hover:opacity-60 transition text-xl leading-none" style="color: #0a1f44;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="px-6 py-5">
            @if($cancelledEvents->count() === 1)
                @php $ev = $cancelledEvents->first(); @endphp
                <p class="text-sm mb-4" style="color: #4b5563;">The following event has been <span class="font-bold" style="color:#0a1f44;">cancelled</span>:</p>
                <div class="border-l-4 pl-4 mb-4 rounded-r-lg py-3 pr-3" style="border-color: #f0a500; background: #fff8e1;">
                    <p class="font-bold text-base" style="color:#0a1f44;">{{ $ev->title }}</p>
                    <p class="text-sm mt-1" style="color:#6b7280;">
                        <i class="fas fa-calendar-times mr-1" style="color:#f0a500;"></i>
                        Was scheduled for: {{ $ev->start_datetime->format('l, j F Y') }}
                        @if($ev->start_datetime->format('H:i') !== '00:00')
                            at {{ $ev->start_datetime->format('g:i A') }}
                        @endif
                    </p>
                    @if($ev->description)
                        <p class="text-sm mt-2" style="color:#374151;">{{ Str::limit($ev->description, 160) }}</p>
                    @endif
                </div>
            @else
                <p class="text-sm mb-4" style="color:#4b5563;">The following events have been <span class="font-bold" style="color:#0a1f44;">cancelled</span>:</p>
                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($cancelledEvents as $ev)
                    <div class="border-l-4 pl-4 pb-3 py-2 pr-2 rounded-r-lg border-b last:border-b-0" style="border-left-color: #f0a500; background: #fff8e1;">
                        <p class="font-bold text-sm" style="color:#0a1f44;">{{ $ev->title }}</p>
                        <p class="text-xs mt-0.5" style="color:#6b7280;">
                            <i class="fas fa-calendar-times mr-1" style="color:#f0a500;"></i>
                            Was scheduled: {{ $ev->start_datetime->format('l, j F Y') }}
                            @if($ev->start_datetime->format('H:i') !== '00:00')
                                at {{ $ev->start_datetime->format('g:i A') }}
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            @endif

            <p class="text-xs mt-4" style="color:#9ca3af;">We apologise for any inconvenience. Please contact us for more information.</p>
        </div>

        {{-- Footer --}}
        <div class="px-6 pb-5 flex items-center justify-between">
            <label class="flex items-center gap-2 text-xs cursor-pointer select-none" style="color:#6b7280;">
                <input type="checkbox" id="dont-show-again" class="rounded">
                Don't show again today
            </label>
            <button onclick="closeCancelledModal()"
                style="background:#f0a500; color:#0a1f44; font-weight:700;"
                class="text-sm px-5 py-2 rounded font-semibold hover:opacity-90 transition">
                I Understand
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // Build a key from all cancelled event IDs + their dates so the cookie changes when events change
    const eventKey = @json($cancelledEvents->map(fn($e) => $e->id . '_' . \Carbon\Carbon::parse($e->start_datetime)->toDateString())->join('-'));
    const cookieName = 'cancelled_seen_' + btoa(eventKey).replace(/=/g,'');

    function getCookie(name) {
        return document.cookie.split('; ').find(r => r.startsWith(name + '='));
    }

    // If they ticked "don't show again today", cookie was set — hide the modal immediately
    if (getCookie(cookieName)) {
        document.getElementById('cancelled-events-modal').style.display = 'none';
    }

    window.closeCancelledModal = function () {
        const modal = document.getElementById('cancelled-events-modal');
        modal.style.opacity = '0';
        modal.style.transition = 'opacity 0.3s';
        setTimeout(() => modal.style.display = 'none', 300);

        if (document.getElementById('dont-show-again').checked) {
            // Expire at midnight tonight
            const midnight = new Date();
            midnight.setHours(23, 59, 59, 0);
            document.cookie = cookieName + '=1; expires=' + midnight.toUTCString() + '; path=/';
        }
    };

    // Close on backdrop click
    document.getElementById('cancelled-events-modal').addEventListener('click', function (e) {
        if (e.target === this) window.closeCancelledModal();
    });
})();
</script>
@endpush
@endif

{{-- HERO SECTION --}}
<section class="relative min-h-screen flex items-center justify-center text-white"
    style="background: linear-gradient(135deg, #0a1f44 0%, #1a3a6b 50%, #0a1f44 100%);">

    @if(!empty($heroImage))
    <div class="absolute inset-0"
        style="background-image: url('{{ asset('storage/' . $heroImage) }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0" style="background: rgba(10,31,68,0.62);"></div>
    </div>
    @else
    <div class="absolute inset-0 opacity-20"
        style="background-image: url('https://images.unsplash.com/photo-1478147427282-58a87a120781?w=1400'); background-size: cover; background-position: center;">
    </div>
    @endif
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">

        <h1 class="text-3xl sm:text-4xl md:text-6xl font-bold mb-4 leading-tight" style="font-family: 'Playfair Display', serif;">
            Welcome to<br>Chrisco Upper Room Fellowship
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl mb-3 italic text-yellow-400">"Where God Dwells"</p>
        <p class="text-base sm:text-lg text-gray-300 mb-8">
            <i class="fas fa-map-marker-alt mr-2 text-yellow-400"></i>Nairobi, Kenya
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('events.index') }}" class="btn-red text-base sm:text-lg font-semibold px-6 sm:px-8 py-3 transition hover:scale-105">
                <i class="fas fa-church mr-2"></i>Join Us This Sunday
            </a>
            <a href="{{ route('livestream') }}" class="border-2 border-white text-white text-base sm:text-lg font-semibold px-6 sm:px-8 py-3 rounded hover:bg-white hover:text-gray-900 transition">
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
@if(!empty($serviceTimes) && collect($serviceTimes)->filter(fn($s) => $s['name'])->isNotEmpty())
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Service Times</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
            @foreach($serviceTimes as $service)
                @if($service['name'])
                <div class="text-center p-6 sm:p-8 rounded-xl shadow-md border-t-4" style="border-color: {{ $service['color'] }};">
                    <i class="{{ $service['icon'] }} text-4xl mb-4" style="color: {{ $service['color'] }};"></i>
                    <h3 class="text-xl font-bold mb-1" style="color: #0a1f44;">{{ $service['name'] }}</h3>
                    @if($service['subtitle'])
                        <p class="text-gray-500 text-sm mb-2">{{ $service['subtitle'] }}</p>
                    @endif
                    <p class="text-2xl font-bold" style="color: #c0392b;">{{ $service['time'] }}</p>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- LATEST SERMONS --}}
<section class="py-16" style="background: #f8fafc;">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Latest Sermons</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
            <p class="text-gray-500 mt-3">Be transformed by the Word of God</p>
        </div>
        @if(isset($sermons) && $sermons->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($sermons as $sermon)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
                        @php $thumb = $sermon->thumbnail ? asset('storage/' . $sermon->thumbnail) : $sermon->youtube_thumbnail; @endphp
                        @if($thumb)
                            <img src="{{ $thumb }}" alt="{{ $sermon->title }}" class="w-full h-44 object-cover">
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
                            <p class="text-gray-500 text-sm mb-1"><i class="fas fa-church mr-1"></i>Chrisco Upper Room</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($events as $event)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 hover:shadow-xl transition">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="w-full h-44 object-cover">
                        @else
                            <div class="w-full h-44 flex items-center justify-center" style="background: linear-gradient(135deg, #0a1f44, #1a3a6b);">
                                <i class="fas fa-calendar-alt text-white text-5xl opacity-30"></i>
                            </div>
                        @endif
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
                                More Details
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
    <div class="max-w-5xl mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44;">Announcements</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        @php $catColors = \App\Models\Announcement::CATEGORY_COLORS; @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($announcements as $ann)
                @php $color = $catColors[$ann->category] ?? '#0a1f44'; @endphp
                <a href="{{ route('announcements.show', $ann) }}"
                    class="bg-white rounded-lg shadow hover:shadow-md transition-shadow p-5 flex items-start space-x-4 border-l-4 group"
                    style="border-color: {{ $color }};">
                    <div class="mt-1 flex-shrink-0">
                        @if($ann->is_pinned)
                            <i class="fas fa-thumbtack text-yellow-400 text-lg"></i>
                        @else
                            <i class="fas fa-bullhorn text-lg" style="color: {{ $color }};"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold px-2 py-0.5 rounded-full text-white" style="background: {{ $color }};">
                                {{ \App\Models\Announcement::CATEGORIES[$ann->category] ?? ucfirst($ann->category) }}
                            </span>
                        </div>
                        <h4 class="font-bold text-gray-800 mb-1 group-hover:text-blue-700 transition-colors">{{ $ann->title }}</h4>
                        <p class="text-gray-500 text-sm">{{ Str::limit(strip_tags($ann->body), 120) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('announcements.index') }}" class="btn-navy px-8 py-3 font-semibold">
                <i class="fas fa-bullhorn mr-2"></i>View All Announcements
            </a>
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



