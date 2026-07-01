@extends('layouts.app')

@section('title', $teaching->title . ' — Chrisco Upper Room Fellowship')

@section('content')

{{-- Hero --}}
<section class="py-10" style="background: #0a1f44;">
    <div class="max-w-4xl mx-auto px-4">
        <a href="{{ route('teachings.index') }}" class="text-gray-400 hover:text-yellow-400 text-sm mb-4 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Teachings
        </a>
        <span class="text-xs font-semibold uppercase tracking-wider px-2 py-1 rounded-full mb-4 inline-block" style="background: #f0a500; color: #0a1f44;">
            {{ $teaching->category ?: 'Teaching' }}
        </span>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4" style="font-family: 'Playfair Display', serif;">
            {{ $teaching->title }}
        </h1>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0" style="background: #c0392b;">
                {{ strtoupper(substr($teaching->pastor_name, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-semibold text-sm">{{ $teaching->pastor_name }}</p>
                <p class="text-gray-400 text-xs">{{ $teaching->created_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- Main Content --}}
            <div class="lg:col-span-2">
                @if($teaching->image)
                    <img src="{{ Storage::url($teaching->image) }}" alt="{{ $teaching->title }}"
                         class="w-full rounded-xl shadow-md mb-8 object-cover max-h-80">
                @endif

                <div class="bg-white rounded-xl shadow p-8 prose max-w-none text-gray-700 leading-relaxed" style="font-size: 1.05rem;">
                    {!! nl2br(e($teaching->content)) !!}
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Pastor card --}}
                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-3" style="background: #0a1f44;">
                        {{ strtoupper(substr($teaching->pastor_name, 0, 1)) }}
                    </div>
                    <p class="font-bold text-gray-800">{{ $teaching->pastor_name }}</p>
                    <p class="text-xs text-gray-400 mt-1">Pastor — Chrisco Upper Room Fellowship</p>
                </div>

                {{-- Related --}}
                @if($related->count())
                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-bold text-gray-800 mb-4" style="font-family: 'Playfair Display', serif;">More Teachings</h3>
                    <div class="space-y-4">
                        @foreach($related as $r)
                        <a href="{{ route('teachings.show', $r) }}" class="flex items-start space-x-3 group">
                            <div class="w-12 h-12 rounded-lg flex-shrink-0 overflow-hidden">
                                @if($r->image)
                                    <img src="{{ Storage::url($r->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background: #0a1f44;">
                                        <i class="fas fa-book-open text-yellow-400 text-sm"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-semibold group-hover:text-red-600 transition-colors" style="color: #0a1f44;">{{ Str::limit($r->title, 50) }}</p>
                                <p class="text-xs text-gray-400">{{ $r->pastor_name }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

@endsection



