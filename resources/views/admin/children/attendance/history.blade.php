@extends('layouts.admin')
@section('title', 'Attendance History')

@push('styles')
<style>
@media print {
    .sidebar, header, .report-actions, .no-print, form { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    .print-header { display: block !important; }
    .mobile-card { display: none !important; }
    .desktop-table { display: table !important; }
}
@page { margin: 1.5cm; }
.print-header { display: none; }
@media (max-width: 640px) {
    .desktop-table { display: none; }
    .mobile-card { display: block; }
}
@media (min-width: 641px) {
    .desktop-table { display: table; }
    .mobile-card { display: none; }
}
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6">

    {{-- Print header --}}
    <div class="print-header mb-6">
        <div style="display:flex;align-items:center;gap:20px;border-bottom:3px solid #0a1f44;padding-bottom:14px;margin-bottom:10px;">
            <img src="{{ asset('images/logo.png') }}" alt="Chrisco Upper Room" style="height:70px;width:auto;object-fit:contain;flex-shrink:0;">
            <div>
                <h1 style="font-size:20px;font-weight:900;color:#0a1f44;line-height:1.1;">Chrisco Upper Room Fellowship</h1>
                <p style="font-size:11px;color:#c0392b;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-top:2px;">Where God Dwells</p>
                <p style="font-size:10px;color:#555;margin-top:4px;">
                    info@chrisco-upper-room.org &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; P.O BOX 61908 Nairobi, Kenya
                </p>
            </div>
        </div>
        <div style="text-align:center;margin-top:6px;">
            <p style="font-size:15px;font-weight:800;color:#0a1f44;text-transform:uppercase;letter-spacing:.5px;">Children Sunday School — Attendance</p>
            <p style="font-size:13px;color:#333;margin-top:3px;font-weight:600;">
                {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}@if($class) &nbsp;|&nbsp; Class: {{ $class }}@endif
            </p>
            <p style="font-size:10px;color:#888;margin-top:2px;">Printed: {{ now()->format('d M Y, g:i A') }}</p>
        </div>
    </div>

    {{-- Top bar --}}
    <div class="mb-5 report-actions no-print">
        <div class="flex items-start justify-between gap-3 mb-1">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold" style="color:#0a1f44;">Attendance</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('admin.children.attendance.report') }}"
                   class="flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 bg-white">
                    <i class="fas fa-chart-bar"></i> <span class="hidden sm:inline">Reports</span>
                </a>
                <button onclick="window.print()"
                        class="flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 bg-white">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                </button>
                <a href="{{ route('admin.children.attendance') }}"
                   class="flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white" style="background:#0a1f44;">
                    <i class="fas fa-camera"></i> <span class="hidden sm:inline">Scanner</span>
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 no-print">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Date</label>
                    <input type="date" name="date" value="{{ $date }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Class</label>
                    <select name="class" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $key => $label)
                        <option value="{{ $key }}" {{ $class == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="px-4 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($records->count())
        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">
                <i class="fas fa-user-check mr-1 text-green-600"></i>
                {{ $records->count() }} present
            </span>
        </div>

        {{-- Desktop table --}}
        <table class="desktop-table w-full text-sm">
            <thead style="background:#0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Child</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Class</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Method</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Confidence</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white no-print">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($records as $i => $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $r->child?->full_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $r->child?->sunday_school_class ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($r->method === 'face')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            <i class="fas fa-camera" style="font-size:0.6rem;"></i> Face
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            <i class="fas fa-hand-pointer" style="font-size:0.6rem;"></i> Manual
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($r->confidence)
                        <div class="flex items-center gap-2">
                            <div class="w-14 bg-gray-200 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $r->confidence >= 80 ? 'bg-green-500' : 'bg-yellow-400' }}"
                                     style="width:{{ $r->confidence }}%"></div>
                            </div>
                            <span class="text-xs text-gray-600">{{ $r->confidence }}%</span>
                        </div>
                        @else <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $r->created_at->format('g:i A') }}</td>
                    <td class="px-4 py-3 no-print">
                        <form method="POST" action="{{ route('admin.children.attendance.remove', $r) }}"
                              data-confirm="Remove this record?" data-confirm-ok="Remove">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Mobile cards --}}
        <div class="mobile-card divide-y divide-gray-100">
            @foreach($records as $i => $r)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm">{{ $r->child?->full_name ?? '—' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $r->child?->sunday_school_class ?? 'No class' }}</p>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            @if($r->method === 'face')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                <i class="fas fa-camera" style="font-size:0.6rem;"></i> Face Recognition
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                <i class="fas fa-hand-pointer" style="font-size:0.6rem;"></i> Manual
                            </span>
                            @endif
                            @if($r->confidence)
                            <span class="text-xs text-gray-500">{{ $r->confidence }}% match</span>
                            @endif
                            <span class="text-xs text-gray-400">{{ $r->created_at->format('g:i A') }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.children.attendance.remove', $r) }}"
                          data-confirm="Remove this record?" data-confirm-ok="Remove" class="flex-shrink-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        @else
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
            <p class="font-medium">No attendance records for this date</p>
            <a href="{{ route('admin.children.attendance') }}" class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                Take attendance now →
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
