@extends('layouts.app')

@section('title', $announcement->title)

@section('content')

@php $catColors = \App\Models\Announcement::CATEGORY_COLORS; $color = $catColors[$announcement->category] ?? '#0a1f44'; @endphp

{{-- Navy header bar --}}
<div class="w-full py-8 flex items-center" style="background: #0a1f44; min-height: 100px;">
    <div class="max-w-3xl mx-auto px-4 w-full">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-xs font-bold px-3 py-1 rounded-full text-white" style="background: {{ $color }};">
                {{ \App\Models\Announcement::CATEGORIES[$announcement->category] ?? ucfirst($announcement->category) }}
            </span>
            @if($announcement->is_pinned)
                <span class="text-xs text-yellow-400 font-semibold"><i class="fas fa-thumbtack mr-1"></i>Pinned</span>
            @endif
        </div>
        <h1 class="text-xl sm:text-3xl font-bold text-white" style="font-family: 'Playfair Display', serif;">
            {{ $announcement->title }}
        </h1>
    </div>
</div>

<section class="py-12" style="background: #f8fafc;">
    <div class="max-w-3xl mx-auto px-4">

        {{-- Back --}}
        <a href="{{ route('announcements.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 mb-6">
            <i class="fas fa-arrow-left mr-2"></i> Back to Announcements
        </a>

        <div class="bg-white rounded-xl shadow-md p-4 sm:p-8">

            {{-- Small image above content --}}
            @if($announcement->image)
                <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}"
                    class="rounded-lg object-cover mb-6"
                    style="max-height: 220px; width: 100%; object-position: center;">
            @endif

            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-6 pb-6 border-b border-gray-100">
                <span><i class="fas fa-calendar-alt mr-1"></i>{{ $announcement->created_at->format('F d, Y') }}</span>
                <span><i class="fas fa-church mr-1"></i>Chrisco Upper Room</span>
                @if($announcement->expires_at)
                    <span><i class="fas fa-clock mr-1"></i>Valid until {{ $announcement->expires_at->format('M d, Y') }}</span>
                @endif
            </div>

            <div class="prose max-w-none text-gray-700 leading-relaxed text-base" style="white-space: pre-wrap;">{{ $announcement->body }}</div>

        </div>

        {{-- Navigation --}}
        <div class="mt-8 text-center">
            <a href="{{ route('announcements.index') }}"
                class="inline-block px-8 py-3 rounded-full font-semibold text-white transition-opacity hover:opacity-80"
                style="background: {{ $color }};">
                <i class="fas fa-th-large mr-2"></i>All Announcements
            </a>
        </div>

    </div>
</section>

@endsection
