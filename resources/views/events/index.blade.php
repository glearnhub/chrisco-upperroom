@extends('layouts.app')

@section('title', 'Upcoming Events — Chrisco Upper Room Fellowship')

@section('content')

<section style="background: #0a1f44; min-height: 220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-calendar-alt text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Upcoming Events</h1>
        <p class="text-gray-300">Join us as we fellowship, worship, and grow together</p>
        @include('partials.updates-subnav')
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        @if($events->count())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($events as $event)
                    @php
                        $dynStatus = $event->dynamic_status;
                        $statusColor = match($dynStatus) {
                            'upcoming'  => 'bg-green-100 text-green-700',
                            'ongoing'   => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition {{ $event->status === 'cancelled' ? 'opacity-80' : '' }}">
                        <div class="relative">
                        @if($event->image)
                            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 flex items-center justify-center" style="background: linear-gradient(135deg, #0a1f44, #1a3a6b);">
                                <i class="fas fa-calendar-star text-white text-5xl opacity-30"></i>
                            </div>
                        @endif
                        @if($event->status === 'cancelled')
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden">
                                <span style="
                                    transform: rotate(-35deg);
                                    color: #ff0000;
                                    font-size: 2.6rem;
                                    font-weight: 900;
                                    letter-spacing: 0.12em;
                                    text-transform: uppercase;
                                    font-family: Arial, sans-serif;
                                    text-shadow: 1px 1px 0 #000, -1px -1px 0 #000, 1px -1px 0 #000, -1px 1px 0 #000;
                                    border: 5px solid #ff0000;
                                    padding: 6px 18px;
                                    white-space: nowrap;
                                    background: rgba(0,0,0,0.35);
                                    -webkit-text-stroke: 1px #cc0000;
                                ">CANCELLED</span>
                            </div>
                        @endif
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $statusColor }}">
                                    {{ ucfirst($dynStatus) }}
                                </span>
                                <span class="text-gray-400 text-xs">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d, Y') }}
                                </span>
                            </div>
                            <h3 class="text-lg font-bold mb-1" style="color: #0a1f44;">{{ $event->title }}</h3>
                            <p class="text-gray-500 text-sm mb-1">
                                <i class="fas fa-clock mr-1 text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}
                            </p>
                            <p class="text-gray-500 text-sm mb-3">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i>{{ $event->location }}
                            </p>
                            @if($event->capacity)
                                <p class="text-gray-400 text-xs mb-3">
                                    <i class="fas fa-users mr-1"></i>Capacity: {{ $event->capacity }}
                                </p>
                            @endif
                            <a href="{{ route('events.show', $event) }}" class="btn-red text-sm w-full text-center block">
                                More Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-10">{{ $events->links() }}</div>
        @else
            <div class="text-center py-20">
                <i class="fas fa-calendar text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No upcoming events at this time. Check back soon!</p>
            </div>
        @endif
    </div>
</section>

@endsection



