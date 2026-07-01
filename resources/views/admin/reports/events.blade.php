@extends('layouts.admin')

@section('title', 'Event Reports')
@section('page-title', 'Event Reports')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow p-8">
        <div class="flex items-center mb-6">
            <i class="fas fa-calendar-check text-3xl mr-3" style="color:#0a1f44;"></i>
            <div>
                <h1 class="text-2xl font-bold" style="color:#0a1f44;">Event Reports</h1>
                <p class="text-gray-500 text-sm">Select an event to generate its registration report.</p>
            </div>
        </div>

        @if($events->count())
            <div class="space-y-3">
                @foreach($events as $event)
                    <a href="{{ route('admin.reports.events.show', $event) }}"
                       class="flex items-center justify-between p-4 rounded-xl border border-gray-200 hover:border-blue-400 hover:shadow-md transition group">
                        <div class="flex items-center gap-4">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" class="w-14 h-14 rounded-lg object-cover">
                            @else
                                <div class="w-14 h-14 rounded-lg flex items-center justify-center" style="background:#0a1f44;">
                                    <i class="fas fa-calendar text-white text-xl opacity-50"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-bold text-gray-800 group-hover:text-blue-700">{{ $event->title }}</p>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d, Y — g:i A') }}
                                    &nbsp;|&nbsp;
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $event->location }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @php
                                $count = $event->registrations()->where('status','!=','cancelled')->count();
                            @endphp
                            <span class="text-sm font-semibold text-white px-3 py-1 rounded-full" style="background:#0a1f44;">
                                {{ $count }} registered
                            </span>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full
                                {{ $event->status === 'cancelled' ? 'bg-red-100 text-red-600' : ($event->status === 'ongoing' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                                {{ ucfirst($event->status) }}
                            </span>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-500"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-calendar text-6xl mb-4"></i>
                <p>No events found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
