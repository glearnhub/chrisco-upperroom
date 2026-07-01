@extends('layouts.admin')

@section('title', 'Manage Events')
@section('page-title', 'Events')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Events</h1>
    <a href="{{ route('admin.events.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Event
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($events->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Capacity</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Registrations</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($events as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $event->title }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('M d, Y g:i A') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $event->location }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $event->capacity ?? 'Unlimited' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $event->registrations_count ?? 0 }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $sc = match($event->status ?? 'upcoming') {
                                        'upcoming'  => 'bg-green-100 text-green-700',
                                        'ongoing'   => 'bg-blue-100 text-blue-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'past'      => 'bg-gray-100 text-gray-500',
                                        default     => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">
                                    {{ ucfirst($event->status ?? 'upcoming') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 flex items-center space-x-2">
                                <a href="{{ route('admin.events.edit', $event) }}"
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                    onsubmit="return confirm('Delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-medium">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $events->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-calendar text-5xl mb-3"></i>
                <p>No events yet. <a href="{{ route('admin.events.create') }}" class="text-blue-600 underline">Create one</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection


