@extends('layouts.admin')
@section('title', 'Attendance Report')

@push('styles')
<style>
@media print {
    .sidebar, header, .report-actions, .no-print, form { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    .report-card { box-shadow: none !important; border: 1px solid #ddd; }
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

    {{-- Top bar --}}
    <div class="mb-5 report-actions no-print">
        <div class="flex items-start justify-between gap-3 mb-1">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold" style="color:#0a1f44;">
                    <i class="fas fa-chart-bar mr-2" style="color:#f0a500;"></i>Attendance Report
                </h1>
                <p class="text-gray-500 text-xs sm:text-sm mt-0.5">All days attendance was recorded</p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <a href="{{ route('admin.children.attendance') }}"
                   class="flex items-center gap-1 px-3 py-2 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-gray-700">
                    <i class="fas fa-camera"></i> <span class="hidden sm:inline">Scanner</span>
                </a>
                <button onclick="window.print()"
                        class="flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white"
                        style="background:#0a1f44;">
                    <i class="fas fa-print"></i> <span class="hidden sm:inline">Print</span>
                </button>
            </div>
        </div>
    </div>

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
            <p style="font-size:15px;font-weight:800;color:#0a1f44;text-transform:uppercase;letter-spacing:.5px;">Children Sunday School — Attendance Report</p>
            <p style="font-size:11px;color:#666;margin-top:3px;">
                {{ $from || $to ? (($from ? \Carbon\Carbon::parse($from)->format('d M Y') : 'All').' — '.($to ? \Carbon\Carbon::parse($to)->format('d M Y') : 'All')) : 'All Dates' }}
                @if($class) &nbsp;|&nbsp; Class: {{ $class }} @endif
                &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, g:i A') }}
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 no-print">
        <form method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="text-xs text-gray-500 block mb-1">Class</label>
                    <select name="class" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $key => $label)
                        <option value="{{ $key }}" {{ $class == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs text-gray-500 block mb-1">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="px-4 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                @if($class || $from || $to)
                <a href="{{ route('admin.children.attendance.report') }}"
                   class="px-4 py-2 text-sm rounded border border-gray-300 text-gray-600">Clear</a>
                @endif
            </div>
        </form>
    </div>

    @if($dates->count())

    {{-- Summary stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 no-print">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Days</p>
            <p class="text-2xl sm:text-3xl font-bold mt-1" style="color:#0a1f44;">{{ $dates->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Present</p>
            <p class="text-2xl sm:text-3xl font-bold mt-1" style="color:#0a1f44;">{{ $dates->sum('total') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Avg / Day</p>
            <p class="text-2xl sm:text-3xl font-bold mt-1" style="color:#f0a500;">
                {{ $dates->count() ? round($dates->sum('total') / $dates->count(), 1) : 0 }}
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 sm:p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Best Day</p>
            <p class="text-2xl sm:text-3xl font-bold mt-1" style="color:#16a34a;">{{ $dates->max('total') }}</p>
        </div>
    </div>

    {{-- Print summary --}}
    <div class="print-header" style="margin-bottom:12px;">
        <div style="display:flex;gap:30px;font-size:11px;color:#333;border:1px solid #ddd;border-radius:6px;padding:8px 14px;background:#f8f9fa;">
            <span><strong>Total Days:</strong> {{ $dates->count() }}</span>
            <span><strong>Total Present:</strong> {{ $dates->sum('total') }}</span>
            <span><strong>Average/Day:</strong> {{ $dates->count() ? round($dates->sum('total') / $dates->count(), 1) : 0 }}</span>
            <span><strong>Best Day:</strong> {{ $dates->max('total') }}</span>
        </div>
    </div>

    {{-- Desktop table --}}
    <div class="report-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="desktop-table w-full text-sm">
            <thead style="background:#0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Day</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Present</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">By Class</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $maxTotal = $dates->max('total'); @endphp
                @foreach($dates as $i => $row)
                @php $dayBreakdown = $breakdown[$row->attendance_date->toDateString()] ?? collect(); @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $row->attendance_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $row->attendance_date->format('l') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-lg font-bold" style="color:#0a1f44;">{{ $row->total }}</span>
                        <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1 mx-auto no-print">
                            <div class="h-1.5 rounded-full" style="background:#f0a500;width:{{ $maxTotal ? round($row->total/$maxTotal*100) : 0 }}%"></div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @if($dayBreakdown->count())
                        <div class="flex flex-wrap gap-1">
                            @foreach($dayBreakdown as $cb)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 no-print">
                                {{ $cb->sunday_school_class ?: 'Unassigned' }}: <strong>{{ $cb->cnt }}</strong>
                            </span>
                            <span class="print-header text-xs text-gray-600">{{ $cb->sunday_school_class ?: 'Unassigned' }}: {{ $cb->cnt }} &nbsp;</span>
                            @endforeach
                        </div>
                        @else <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 no-print">
                        <a href="{{ route('admin.children.attendance.history', ['date' => $row->attendance_date->toDateString(), 'class' => $class]) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs rounded font-semibold text-white" style="background:#0a1f44;">
                            <i class="fas fa-list"></i> View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-700">TOTAL</td>
                    <td class="px-4 py-3 text-center text-sm font-bold" style="color:#0a1f44;">{{ $dates->sum('total') }}</td>
                    <td colspan="2" class="px-4 py-3 text-xs text-gray-400">across {{ $dates->count() }} day(s)</td>
                </tr>
            </tfoot>
        </table>

        {{-- Mobile cards --}}
        <div class="mobile-card divide-y divide-gray-100">
            @php $maxTotal = $dates->max('total'); @endphp
            @foreach($dates as $i => $row)
            @php $dayBreakdown = $breakdown[$row->attendance_date->toDateString()] ?? collect(); @endphp
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 text-sm">{{ $row->attendance_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $row->attendance_date->format('l') }}</p>
                        @if($dayBreakdown->count())
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($dayBreakdown as $cb)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $cb->sunday_school_class ?: 'Unassigned' }}: {{ $cb->cnt }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-2xl font-bold" style="color:#0a1f44;">{{ $row->total }}</p>
                        <p class="text-xs text-gray-400">present</p>
                        <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1 ml-auto">
                            <div class="h-1.5 rounded-full" style="background:#f0a500;width:{{ $maxTotal ? round($row->total/$maxTotal*100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.children.attendance.history', ['date' => $row->attendance_date->toDateString(), 'class' => $class]) }}"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs rounded font-semibold text-white w-full justify-center" style="background:#0a1f44;">
                        <i class="fas fa-list"></i> View Records
                    </a>
                </div>
            </div>
            @endforeach
            <div class="px-4 py-3" style="background:#f8fafc;">
                <div class="flex justify-between text-sm font-bold text-gray-700">
                    <span>TOTAL ({{ $dates->count() }} days)</span>
                    <span style="color:#0a1f44;">{{ $dates->sum('total') }}</span>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 text-center py-12 text-gray-400">
        <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
        <p class="font-medium text-gray-600">No attendance records found</p>
        <p class="text-sm mt-1">
            @if($class || $from || $to)
                <a href="{{ route('admin.children.attendance.report') }}" class="text-blue-600 hover:underline">Clear filters</a>
            @else
                Use the scanner to take attendance first.
            @endif
        </p>
        <a href="{{ route('admin.children.attendance') }}"
           class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 text-sm rounded text-white font-semibold no-print" style="background:#0a1f44;">
            <i class="fas fa-camera"></i> Open Scanner
        </a>
    </div>
    @endif

</div>
@endsection
