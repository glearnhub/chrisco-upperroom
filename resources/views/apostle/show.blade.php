@extends('layouts.app')

@section('title', $apostleTeaching->title . ' — Apostle Das Teachings')

@section('content')

<section style="background:#0a1f44; min-height:220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-video text-5xl mb-3" style="color:#f0a500;"></i>
        <h1 class="text-3xl font-bold text-white mb-2">Apostle Das Teachings</h1>
        @include('partials.resources-subnav')
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Back --}}
        <a href="{{ route('apostle.index') }}" class="inline-flex items-center text-sm mb-6 hover:underline" style="color:#0a1f44;">
            <i class="fas fa-arrow-left mr-2"></i>Back to Teachings
        </a>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Video --}}
            <div class="w-full" style="aspect-ratio:16/9; background:#000;">
                <iframe src="{{ $apostleTeaching->embed_url }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

            <div class="p-4 sm:p-6 md:p-8">
                @if($apostleTeaching->category)
                    <span class="text-xs font-semibold px-2 py-1 rounded-full text-white" style="background:#c0392b;">
                        {{ $apostleTeaching->category->name }}
                    </span>
                @endif
                <h1 class="text-2xl md:text-3xl font-bold mt-3 mb-4" style="color:#0a1f44;">{{ $apostleTeaching->title }}</h1>
                <p class="text-gray-400 text-sm mb-4">
                    <i class="fas fa-eye mr-1"></i>{{ number_format($apostleTeaching->views) }} views
                </p>
                @if($apostleTeaching->description)
                    <div class="text-gray-600 leading-relaxed border-t pt-4">
                        {!! nl2br(e($apostleTeaching->description)) !!}
                    </div>
                @endif
            </div>
        </div>

        {{-- Related --}}
        @if($related->count())
        <div class="mt-12">
            <h2 class="text-xl font-bold mb-5" style="color:#0a1f44;">More Teachings</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($related as $item)
                    @php
                        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/live\/)([a-zA-Z0-9_-]{11})/', $item->youtube_url, $ym);
                        $ytThumb = isset($ym[1]) ? 'https://img.youtube.com/vi/' . $ym[1] . '/hqdefault.jpg' : null;
                    @endphp
                    <a href="{{ route('apostle.show', $item) }}" class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden group block">
                        @if($item->thumbnail)
                            <img src="{{ asset('storage/' . $item->thumbnail) }}" class="w-full h-36 object-cover group-hover:scale-105 transition duration-300" alt="{{ $item->title }}">
                        @elseif($ytThumb)
                            <div class="relative w-full h-36 overflow-hidden">
                                <img src="{{ $ytThumb }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $item->title }}">
                                <div class="absolute inset-0 flex items-center justify-center" style="background:rgba(0,0,0,0.3);">
                                    <i class="fab fa-youtube text-4xl text-white opacity-80"></i>
                                </div>
                            </div>
                        @else
                            <div class="w-full h-36 flex items-center justify-center" style="background:#0a1f44;">
                                <i class="fas fa-video text-white text-3xl opacity-30"></i>
                            </div>
                        @endif
                        <div class="p-4">
                            <p class="text-sm font-semibold leading-snug" style="color:#0a1f44;">{{ $item->title }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@endsection
