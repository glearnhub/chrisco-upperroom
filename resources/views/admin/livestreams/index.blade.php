@extends('layouts.admin')

@section('title', 'Livestreams')
@section('page-title', 'Livestream Management')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Livestreams</h1>
    <a href="{{ route('admin.livestreams.create') }}" class="btn-red px-4 py-2 text-sm">
        <i class="fas fa-plus mr-2"></i>Add Livestream
    </a>
</div>

@if(session('success'))
<div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
@endif

@php $liveCount = $livestreams->where('is_live', true)->count(); @endphp

{{-- Active stream warning banner --}}
@if($liveCount)
<div class="mb-5 rounded-xl border-2 border-red-500 bg-red-50 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        <span class="relative flex h-4 w-4 flex-shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-600"></span>
        </span>
        <div>
            <p class="font-bold text-red-800 text-sm">{{ $liveCount }} stream{{ $liveCount > 1 ? 's' : '' }} currently LIVE on the website</p>
            <p class="text-xs text-red-600 mt-0.5">Visitors can see this as active. Click "End All Streams" once the broadcast is over.</p>
        </div>
    </div>
    <form method="POST" action="{{ route('admin.livestreams.endAll') }}"
          onsubmit="return confirm('End all active livestreams?')">
        @csrf
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-700 whitespace-nowrap">
            <i class="fas fa-stop-circle"></i> End All Streams
        </button>
    </form>
</div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($livestreams->count())
        <table class="w-full text-sm">
            <thead style="background:#0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Platform</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Scheduled</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($livestreams as $ls)
                <tr class="hover:bg-gray-50 {{ $ls->is_live ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $ls->title }}
                        @if($ls->is_live)
                        <span class="ml-2 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-bold bg-red-600 text-white">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse inline-block"></span> LIVE
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $ls->platform ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        {{ $ls->scheduled_at ? $ls->scheduled_at->format('M d, Y g:i A') : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.livestreams.toggleLive', $ls) }}">
                            @csrf @method('PATCH')
                            @if($ls->is_live)
                            <button type="submit"
                                    onclick="return confirm('End this livestream? It will no longer show as live on the website.')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-600 text-white hover:bg-red-700">
                                <i class="fas fa-stop-circle"></i> End Stream
                            </button>
                            @else
                            <button type="submit"
                                    onclick="return confirm('Go live with this stream? It will appear as LIVE on the website.')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-200 text-gray-700 hover:bg-green-100 hover:text-green-800">
                                <i class="fas fa-play-circle"></i> Go Live
                            </button>
                            @endif
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.livestreams.edit', $ls) }}"
                               class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form method="POST" action="{{ route('admin.livestreams.destroy', $ls) }}"
                                  onsubmit="return confirm('Delete this livestream?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-broadcast-tower text-5xl mb-3 block"></i>
            <p>No livestreams yet. <a href="{{ route('admin.livestreams.create') }}" class="text-blue-600 underline">Add one</a>.</p>
        </div>
        @endif
    </div>
</div>

@endsection
