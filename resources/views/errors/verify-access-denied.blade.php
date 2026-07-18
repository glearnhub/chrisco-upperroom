@extends('layouts.app')

@section('title', 'Access Denied — Member Verification')

@section('content')
<section style="background:#0a1f44; min-height:220px; display:flex; align-items:center;">
    <div class="max-w-7xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-lock text-5xl mb-3" style="color:#f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Access Denied</h1>
        <p class="text-gray-300">Member verification is not available at this time.</p>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-lg mx-auto px-4 text-center">
        <div class="bg-white rounded-2xl shadow-lg p-10">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background:#fef3c7;">
                <i class="fas fa-calendar-times text-4xl" style="color:#f0a500;"></i>
            </div>
            <h2 class="text-2xl font-bold mb-3" style="color:#0a1f44;">Verification Window Closed</h2>

            @if($start && $end)
                <p class="text-gray-500 mb-4">
                    Member verification is only available from<br>
                    <strong class="text-gray-800">{{ $start }}</strong> to <strong class="text-gray-800">{{ $end }}</strong>.
                </p>
            @elseif($start)
                <p class="text-gray-500 mb-4">
                    Member verification opens on <strong class="text-gray-800">{{ $start }}</strong>.
                </p>
            @elseif($end)
                <p class="text-gray-500 mb-4">
                    Member verification closed on <strong class="text-gray-800">{{ $end }}</strong>.
                </p>
            @else
                <p class="text-gray-500 mb-4">Member verification is currently unavailable.</p>
            @endif

            <p class="text-gray-400 text-sm">Please contact the church office for assistance.</p>

            <a href="{{ route('home') }}" class="inline-block mt-6 px-6 py-3 rounded-full text-white font-semibold text-sm"
               style="background:#0a1f44;">
                <i class="fas fa-home mr-2"></i>Back to Home
            </a>
        </div>
    </div>
</section>
@endsection
