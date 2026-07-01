@extends('layouts.app')

@section('title', 'About Us — Chrisco Upper Room Fellowship')

@section('content')

{{-- PAGE HERO --}}
<section class="py-20 text-white text-center" style="background: linear-gradient(135deg, #0a1f44 0%, #1a3a6b 100%);">
    <div class="max-w-3xl mx-auto px-4">
        <i class="fas fa-church text-yellow-400 text-5xl mb-4"></i>
        <h1 class="text-4xl md:text-5xl font-bold mb-3" style="font-family: 'Playfair Display', serif;">About Us</h1>
        <p class="text-xl italic text-yellow-400">"Where God Dwells"</p>
        <p class="text-gray-300 mt-3">Chrisco Upper Room Fellowship — Nairobi, Kenya</p>
    </div>
</section>

{{-- WHO WE ARE --}}
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44; font-family: 'Playfair Display', serif;">Who We Are</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
        </div>
        @if($whoWeAre)
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed text-center">
                {!! nl2br(e($whoWeAre)) !!}
            </div>
        @else
            <p class="text-center text-gray-400 italic">Church information coming soon.</p>
        @endif
    </div>
</section>

{{-- VISION & MISSION --}}
<section class="py-16" style="background: #f8fafc;">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- Vision --}}
            <div class="bg-white rounded-2xl shadow-md p-8 border-t-4" style="border-color: #0a1f44;">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4" style="background: #0a1f44;">
                        <i class="fas fa-eye text-yellow-400 text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold" style="color: #0a1f44; font-family: 'Playfair Display', serif;">Our Vision</h3>
                </div>
                @if($vision)
                    <p class="text-gray-700 leading-relaxed">{{ $vision }}</p>
                @else
                    <p class="text-gray-400 italic">Vision statement coming soon.</p>
                @endif
            </div>

            {{-- Mission --}}
            <div class="bg-white rounded-2xl shadow-md p-8 border-t-4" style="border-color: #c0392b;">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4" style="background: #c0392b;">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold" style="color: #0a1f44; font-family: 'Playfair Display', serif;">Our Mission</h3>
                </div>
                @if($mission)
                    <p class="text-gray-700 leading-relaxed">{{ $mission }}</p>
                @else
                    <p class="text-gray-400 italic">Mission statement coming soon.</p>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- CORE PILLARS --}}
@if($pillars->count())
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44; font-family: 'Playfair Display', serif;">Core Pillars</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
            <p class="text-gray-500 mt-3">The foundations upon which we stand</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($pillars as $pillar)
                <div class="text-center p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background: #0a1f44;">
                        <i class="{{ $pillar->icon }} text-yellow-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-bold mb-2" style="color: #0a1f44;">{{ $pillar->title }}</h4>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ $pillar->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CHURCH LEADERSHIP --}}
<section class="py-16" style="background: #f8fafc;">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-2" style="color: #0a1f44; font-family: 'Playfair Display', serif;">Church Leadership</h2>
            <div class="w-16 h-1 mx-auto rounded" style="background: #c0392b;"></div>
            <p class="text-gray-500 mt-3">Servants of God leading this ministry</p>
        </div>

        {{-- Founder --}}
        @if($founder)
            <div class="mb-12">
                <h3 class="text-center text-sm font-bold uppercase tracking-widest mb-6 text-gray-400">Founder</h3>
                <div class="max-w-sm mx-auto">
                    @include('about._leader_card', ['leader' => $founder, 'featured' => true])
                </div>
            </div>
        @endif

        {{-- Bishop / Senior Pastor --}}
        @if($bishops->count())
            <div class="mb-12">
                <h3 class="text-center text-sm font-bold uppercase tracking-widest mb-6 text-gray-400">Residing Pastor / Bishop</h3>
                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($bishops as $leader)
                        @include('about._leader_card', ['leader' => $leader, 'featured' => true])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Pastors --}}
        @if($pastors->count())
            <div class="mb-12">
                <h3 class="text-center text-sm font-bold uppercase tracking-widest mb-6 text-gray-400">Pastors</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($pastors as $leader)
                        @include('about._leader_card', ['leader' => $leader])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Other Leaders --}}
        @if($others->count())
            <div>
                <h3 class="text-center text-sm font-bold uppercase tracking-widest mb-6 text-gray-400">Church Leaders</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($others as $leader)
                        @include('about._leader_card', ['leader' => $leader])
                    @endforeach
                </div>
            </div>
        @endif

        @if(!$founder && !$bishops->count() && !$pastors->count() && !$others->count())
            <p class="text-center text-gray-400 italic">Leadership information coming soon.</p>
        @endif
    </div>
</section>

@endsection



