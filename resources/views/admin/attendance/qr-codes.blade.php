@extends('layouts.admin')

@section('title', 'QR Codes')
@section('page-title', 'Attendance QR Codes')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.attendance.index') }}" class="text-sm text-gray-400 hover:text-gray-600 mb-1 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Back to Attendance
    </a>
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">QR Codes</h1>
    <p class="text-gray-500 text-sm mt-1">Print these once and mount on each door permanently. They never change.</p>
</div>

<div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6 text-sm text-amber-800">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>Print once, laminate, done.</strong> These 3 QR codes are permanent. Every Sunday the system finds the open session automatically when a member scans.
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
    @foreach(['A' => 'Main Entrance', 'B' => 'Side Door', 'C' => 'Back Door'] as $door => $label)
    <div class="bg-white rounded-xl shadow-sm p-6 text-center">
        <h2 class="text-base font-bold mb-1" style="color:#0a1f44;">Door {{ $door }}</h2>
        <p class="text-sm text-gray-400 mb-4">{{ $label }}</p>
        <div class="w-40 h-40 mx-auto mb-4 border border-gray-100 rounded-lg overflow-hidden">
            <img src="{{ route('qr.generate', ['url' => url('/attend?door='.$door), 'size' => 160]) }}"
                 alt="QR Code Door {{ $door }}"
                 class="w-full h-full object-contain">
        </div>
        <p class="text-xs font-mono text-gray-400 mb-4">/attend?door={{ $door }}</p>
        <a href="{{ route('qr.generate', ['url' => url('/attend?door='.$door), 'size' => 600]) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold"
           style="background:#0a1f44;color:#f0a500;">
            <i class="fas fa-download"></i> Download (600×600)
        </a>
    </div>
    @endforeach
</div>

<div class="mt-6 bg-white rounded-xl shadow-sm p-5">
    <h2 class="text-base font-bold mb-3" style="color:#0a1f44;">Check-In URL for Manual Sharing</h2>
    <p class="text-sm text-gray-500 mb-3">If members can't scan, they can type this URL in their browser:</p>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach(['A','B','C'] as $door)
        <div class="bg-gray-50 rounded-lg px-4 py-3">
            <p class="text-xs font-semibold text-gray-500 mb-1">Door {{ $door }}</p>
            <p class="text-sm font-mono" style="color:#0a1f44;">{{ url('/attend?door='.$door) }}</p>
        </div>
        @endforeach
    </div>
</div>

@endsection
