@extends('layouts.app')

@section('title', 'My Dashboard — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-white">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-300 mt-1">Member Dashboard — Chrisco Upper Room Fellowship</p>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Stats Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-xl shadow p-6 flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: #0a1f44;">
                    <i class="fas fa-hand-holding-usd text-yellow-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Donated</p>
                    <p class="text-2xl font-bold" style="color: #0a1f44;">
                        KES {{ number_format($totalDonated ?? 0, 2) }}
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: #c0392b;">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Events Registered</p>
                    <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $eventsCount ?? 0 }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: #f0a500;">
                    <i class="fas fa-praying-hands text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Prayer Requests</p>
                    <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $prayersCount ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Recent Givings --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-lg" style="color: #0a1f44;">Recent Givings</h2>
                    <a href="{{ route('give') }}" class="btn-red text-xs px-3 py-1">Give Again</a>
                </div>
                <div class="overflow-x-auto">
                    @if(isset($recentDonations) && $recentDonations->count())
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentDonations as $donation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-600">
                                            {{ \Carbon\Carbon::parse($donation->created_at)->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 capitalize">{{ str_replace('_', ' ', $donation->giving_type) }}</td>
                                        <td class="px-4 py-3 font-semibold" style="color: #0a1f44;">KES {{ number_format($donation->amount, 2) }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $sc = match($donation->status ?? 'pending') {
                                                    'confirmed' => 'bg-green-100 text-green-700',
                                                    'pending'   => 'bg-yellow-100 text-yellow-700',
                                                    'rejected'  => 'bg-red-100 text-red-700',
                                                    default     => 'bg-gray-100 text-gray-600',
                                                };
                                            @endphp
                                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $sc }}">
                                                {{ ucfirst($donation->status ?? 'pending') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 text-sm text-center py-8">No givings yet. <a href="{{ route('give') }}" class="text-red-600 underline">Give today</a>.</p>
                    @endif
                </div>
            </div>

            {{-- Upcoming Events --}}
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-lg" style="color: #0a1f44;">My Events</h2>
                    <a href="{{ route('events.index') }}" class="btn-navy text-xs px-3 py-1">Browse Events</a>
                </div>
                <div class="p-4">
                    @if(isset($registeredEvents) && $registeredEvents->count())
                        <div class="space-y-3">
                            @foreach($registeredEvents as $event)
                                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background: #0a1f44;">
                                        <i class="fas fa-calendar text-yellow-400 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm" style="color: #0a1f44;">{{ $event->title }}</p>
                                        <p class="text-gray-400 text-xs">
                                            {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d, Y g:i A') }}
                                        </p>
                                        <p class="text-gray-400 text-xs">{{ $event->location }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-6">You haven't registered for any events.</p>
                    @endif
                </div>
            </div>

            {{-- Recent Prayer Requests --}}
            <div class="bg-white rounded-xl shadow md:col-span-2">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h2 class="font-bold text-lg" style="color: #0a1f44;">My Prayer Requests</h2>
                    <a href="{{ route('prayer.index') }}" class="btn-red text-xs px-3 py-1">Submit New</a>
                </div>
                <div class="p-4">
                    @if(isset($recentPrayers) && $recentPrayers->count())
                        <div class="space-y-3">
                            @foreach($recentPrayers as $prayer)
                                <div class="p-4 bg-gray-50 rounded-lg border-l-4" style="border-color: #0a1f44;">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($prayer->created_at)->format('M d, Y') }}</span>
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $prayer->status === 'answered' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($prayer->status ?? 'pending') }}
                                        </span>
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ Str::limit($prayer->request, 150) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm text-center py-6">No prayer requests submitted yet.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

@endsection



