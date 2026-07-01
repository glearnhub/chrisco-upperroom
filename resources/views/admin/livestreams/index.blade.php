@extends('layouts.admin')

@section('title', 'Livestreams')
@section('page-title', 'Livestream Management')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Livestreams</h1>
    <a href="{{ route('admin.livestreams.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Livestream
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($livestreams->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Platform</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Scheduled</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Live Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($livestreams as $ls)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $ls->title }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $ls->platform ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $ls->scheduled_at ? \Carbon\Carbon::parse($ls->scheduled_at)->format('M d, Y g:i A') : '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.livestreams.toggleLive', $ls) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-3 py-1 rounded-full text-xs font-bold {{ $ls->is_live ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-600' }} hover:opacity-80 transition">
                                        {{ $ls->is_live ? '🔴 LIVE' : '⚫ OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3 flex items-center space-x-2">
                                <a href="{{ route('admin.livestreams.edit', $ls) }}"
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.livestreams.destroy', $ls) }}"
                                    onsubmit="return confirm('Delete this livestream?')">
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
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-broadcast-tower text-5xl mb-3"></i>
                <p>No livestreams yet. <a href="{{ route('admin.livestreams.create') }}" class="text-blue-600 underline">Add one</a>.</p>
            </div>
        @endif
    </div>
</div>

@endsection


