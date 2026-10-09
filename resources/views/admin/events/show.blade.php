@extends('layouts.admin')

@section('title', $event->title)
@section('page-title', 'Event Details')

@push('styles')
<style>
@media print {
    .sidebar, #admin-sidebar, header, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    main { padding: 0 !important; }
    .print-header { display: block !important; }
    body { background: white; }
    table { width: 100%; page-break-inside: auto; }
    tr { page-break-inside: avoid; }
    thead { display: table-header-group; }
}
@page { margin: 1.5cm; }
.print-header { display: none; }
</style>
@endpush

@section('content')

{{-- Print Letterhead (hidden on screen) --}}
<div class="print-header mb-6">
    <div style="display:flex;align-items:center;gap:20px;border-bottom:3px solid #0a1f44;padding-bottom:14px;margin-bottom:10px;">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:70px;width:auto;object-fit:contain;">
        @endif
        <div>
            <h1 style="font-size:1.3rem;font-weight:800;color:#0a1f44;margin:0;">Chrisco Upper Room Fellowship</h1>
            <p style="color:#c0392b;font-style:italic;margin:2px 0 0;">Where God Dwells</p>
            <p style="font-size:0.75rem;color:#555;margin:4px 0 0;">P.O BOX 61908, Nairobi, Kenya &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; info@chrisco-upper-room.org</p>
        </div>
    </div>
    <div style="text-align:center;margin-bottom:8px;">
        <h2 style="font-size:1.1rem;font-weight:800;color:#0a1f44;margin:0;">{{ $event->title }} Report</h2>
        @if($event->location)<p style="font-size:0.82rem;color:#555;margin:3px 0 0;">{{ $event->location }}</p>@endif
    </div>
    @php $allRegsCount = $event->registrations->where('status','!=','cancelled')->count(); @endphp
    <div style="display:flex;justify-content:space-between;font-size:0.78rem;color:#444;padding:6px 0;border-top:1px solid #ddd;border-bottom:1px solid #ddd;margin-bottom:6px;">
        <div>{{ $event->start_datetime->format('F d, Y') }}</div>
        <div><strong>Total Registered: {{ $allRegsCount }}</strong></div>
        <div><strong>Total Attended: {{ $event->registrations->where('attended', true)->count() }}</strong></div>
    </div>
</div>

<div class="mb-5 no-print">
    <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
        <i class="fas fa-arrow-left text-xs"></i> Back to Events
    </a>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">{{ $event->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-clock mr-1"></i>{{ $event->start_datetime->format('D, d M Y · g:i A') }}
                @if($event->location) &nbsp;·&nbsp; <i class="fas fa-map-marker-alt mr-1"></i>{{ $event->location }} @endif
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button onclick="window.print()"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="{{ route('admin.events.edit', $event) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.events.checkin', $event) }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
               style="background:#0a1f44;">
                <i class="fas fa-user-check"></i> Usher Check-In Panel
            </a>
        </div>
    </div>
</div>

{{-- Stats row --}}
@php
    $total    = $event->registrations->where('status','!=','cancelled')->count();
    $attended = $event->registrations->where('attended', true)->count();
    $pending  = $total - $attended;
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6 no-print">
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <p class="text-3xl font-extrabold" style="color:#0a1f44;">{{ $total }}</p>
        <p class="text-xs text-gray-500 mt-1">Registered</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <p class="text-3xl font-extrabold" style="color:#16a34a;">{{ $attended }}</p>
        <p class="text-xs text-gray-500 mt-1">Attended</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <p class="text-3xl font-extrabold" style="color:#f59e0b;">{{ $pending }}</p>
        <p class="text-xs text-gray-500 mt-1">Not Checked In</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <p class="text-3xl font-extrabold" style="color:#6366f1;">{{ $total > 0 ? round($attended/$total*100) : 0 }}%</p>
        <p class="text-xs text-gray-500 mt-1">Attendance Rate</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- QR Code Card --}}
    <div class="bg-white rounded-xl shadow-sm p-5 text-center no-print">
        <h2 class="text-base font-bold mb-1" style="color:#0a1f44;"><i class="fas fa-qrcode mr-2 text-gray-400"></i>Event QR Code</h2>
        <p class="text-xs text-gray-400 mb-4">Members scan this to check in at the door</p>

        @php $attendUrl = request()->root() . '/attend/event/' . $event->id; @endphp
        <div class="w-44 h-44 mx-auto mb-3 border border-gray-100 rounded-xl overflow-hidden">
            <img src="{{ request()->root() }}/qr?url={{ urlencode($attendUrl) }}&size=176"
                 alt="Event QR" class="w-full h-full object-contain">
        </div>
        <p class="text-xs font-mono text-gray-400 mb-4">/attend/event/{{ $event->id }}</p>

        @php
            $now    = now();
            $open   = $event->start_datetime->copy()->subHours(2);
            $close  = $event->end_datetime ?? $event->start_datetime->copy()->addHours(8);
            $isOpen = $now->between($open, $close) && $event->status !== 'cancelled';
        @endphp

        @if($isOpen)
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold mb-3" style="background:#dcfce7;color:#15803d;">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span> Check-in is OPEN
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold mb-3 bg-gray-100 text-gray-500">
            <i class="fas fa-lock text-xs"></i>
            {{ $now->lt($open) ? 'Opens ' . $open->format('g:i A') : 'Check-in closed' }}
        </span>
        @endif

        <div class="flex gap-2 justify-center flex-wrap">
            <a href="{{ request()->root() }}/qr?url={{ urlencode($attendUrl) }}&size=600"
               target="_blank"
               class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold"
               style="background:#0a1f44;color:#f0a500;">
                <i class="fas fa-download"></i> Download
            </a>
            <a href="{{ $attendUrl }}" target="_blank"
               class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border border-gray-300 text-gray-600 hover:bg-gray-50">
                <i class="fas fa-external-link-alt"></i> Preview
            </a>
        </div>
    </div>

    {{-- Registrations list --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold" style="color:#0a1f44;">Registrations</h2>
        </div>
        <div class="overflow-x-auto">
            @php $allRegs = $event->registrations->where('status','!=','cancelled'); @endphp
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                        <th class="px-5 py-3">S/No</th>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3 text-center">Attended</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($allRegs as $i => $reg)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-semibold" style="color:#0a1f44;">{{ $reg->full_name }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $reg->phone ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs">
                            <span class="px-2 py-0.5 rounded-full {{ $reg->member_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $reg->member_id ? 'Church Member' : 'Visitor' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($reg->attended)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold" style="background:#ede9fe;color:#7c3aed;">
                                    <i class="fas fa-check-circle"></i> Present
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No registrations yet.</td></tr>
                    @endforelse
                </tbody>
                @if($allRegs->count())
                @php $attendedCount = $allRegs->where('attended', true)->count(); @endphp
                <tfoot>
                    <tr style="background:#0a1f44;">
                        <td colspan="3" class="px-5 py-3 text-white font-bold text-sm">Summary</td>
                        <td class="px-5 py-3 text-white text-sm"><span class="font-bold">{{ $allRegs->count() }}</span> Registered</td>
                        <td class="px-5 py-3 text-white text-sm text-center">
                            <span class="font-bold">{{ $attendedCount }}</span> Attended
                            <span class="text-gray-300 text-xs ml-1">({{ $allRegs->count() > 0 ? round($attendedCount / $allRegs->count() * 100) : 0 }}%)</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

@endsection
