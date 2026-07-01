@extends('layouts.app')

@section('title', $sermon->title . ' — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-4xl mx-auto px-4">
        <a href="{{ route('sermons.index') }}" class="text-yellow-400 hover:text-yellow-300 text-sm mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Sermons
        </a>
        <h1 class="text-3xl md:text-4xl font-bold text-white leading-tight">{{ $sermon->title }}</h1>
        <div class="flex flex-wrap gap-4 mt-4 text-gray-300 text-sm">
            <span><i class="fas fa-user mr-1 text-yellow-400"></i>{{ $sermon->speaker }}</span>
            <span><i class="fas fa-calendar mr-1 text-yellow-400"></i>{{ \Carbon\Carbon::parse($sermon->sermon_date)->format('F d, Y') }}</span>
            @if($sermon->scripture)
                <span><i class="fas fa-book mr-1 text-yellow-400"></i>{{ $sermon->scripture }}</span>
            @endif
            @if($sermon->series)
                <span class="px-2 py-0.5 rounded text-xs font-semibold text-white" style="background: #c0392b;">{{ $sermon->series }}</span>
            @endif
        </div>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Video Player --}}
        @if($sermon->video_url)
            @php
                $videoId = null;
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $sermon->video_url, $m)) {
                    $videoId = $m[1];
                }
            @endphp
            @if($videoId)
                <div class="relative mb-8 rounded-xl overflow-hidden shadow-xl" style="padding-top: 56.25%;">
                    <iframe class="absolute inset-0 w-full h-full"
                        src="https://www.youtube.com/embed/{{ $videoId }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="mb-8 rounded-xl overflow-hidden shadow-xl" style="padding-top: 56.25%; position: relative;">
                    <iframe class="absolute inset-0 w-full h-full" src="{{ $sermon->video_url }}" frameborder="0" allowfullscreen></iframe>
                </div>
            @endif
        @endif

        {{-- Audio Player --}}
        @if($sermon->audio_url)
            <div class="mb-8 bg-white rounded-xl shadow p-6">
                <h3 class="font-semibold text-gray-700 mb-3"><i class="fas fa-headphones mr-2 text-red-600"></i>Listen to Sermon</h3>
                <audio controls class="w-full">
                    <source src="{{ $sermon->audio_url }}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        @endif

        {{-- Sermon Details --}}
        <div class="bg-white rounded-xl shadow p-6">
            @if($sermon->thumbnail && !$sermon->video_url)
                <img src="{{ asset('storage/' . $sermon->thumbnail) }}" alt="{{ $sermon->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
            @endif

            @if($sermon->description)
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($sermon->description)) !!}
                </div>
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ route('sermons.index') }}" class="btn-navy">
                <i class="fas fa-arrow-left mr-2"></i>Back to All Sermons
            </a>
        </div>
    </div>
</section>

@endsection



