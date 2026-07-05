@extends('layouts.admin')

@section('title', 'Full Membership Report')
@section('page-title', 'Reports')

@push('styles')
<style>
@media print {
    .sidebar, header, .report-actions, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    .report-card { box-shadow: none !important; border: 1px solid #ddd; }
    .page-break { page-break-before: always; }
    .print-header { display: block !important; }
}
@page {
    margin: 1.5cm;
    @bottom-center {
        content: "Page " counter(page) " of " counter(pages);
        font-size: 9pt;
        color: #555;
    }
}
.print-header { display: none; }
</style>
@endpush

@section('content')

{{-- Top bar with actions --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 report-actions no-print">
    <div>
        <h1 class="text-2xl font-bold" style="color: #0a1f44;">Full Membership Report</h1>
        <p class="text-gray-500 text-sm mt-1">All members sorted by office hierarchy &mdash; {{ $members->count() }} total</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        {{-- Reports dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div class="absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50 hidden group-hover:block">
                <a href="{{ route('admin.reports.membership') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 font-semibold" style="color:#0a1f44;">
                    <i class="fas fa-users w-4"></i> Full Membership
                </a>
                <a href="{{ route('admin.reports.leaders') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-crown w-4"></i> Leaders Report
                </a>
                <a href="{{ route('admin.reports.mentorship') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-user-shield w-4"></i> Mentorship Report
                </a>
                <a href="{{ route('admin.reports.children-parent') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-child w-4"></i> Children by Parent
                </a>
                <a href="{{ route('admin.reports.by-department') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-layer-group w-4"></i> By Department
                </a>
            </div>
        </div>
        <a href="{{ route('admin.reports.membership', ['export' => 'excel']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#1d6f42;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <button onclick="openPrintDialogMulti('.report-card table', 'Full Membership Report', 'Total: {{ $members->count() }} members')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print / PDF
        </button>
    </div>
</div>

<div class="print-header mb-6">
    <div style="display:flex; align-items:center; gap:20px; border-bottom:3px solid #0a1f44; padding-bottom:14px; margin-bottom:10px;">
        <img src="{{ asset('images/logo.png') }}" alt="Chrisco Upper Room" style="height:70px; width:auto; object-fit:contain; flex-shrink:0;">
        <div>
            <h1 style="font-size:20px; font-weight:900; color:#0a1f44; line-height:1.1;">Chrisco Upper Room Fellowship</h1>
            <p style="font-size:11px; color:#c0392b; font-weight:700; text-transform:uppercase; letter-spacing:.8px; margin-top:2px;">Where God Dwells</p>
            <p style="font-size:10px; color:#555; margin-top:4px;">
                <span>info@chrisco-upper-room.org</span> &nbsp;|&nbsp;
                <span>+254 726 900 700</span> &nbsp;|&nbsp;
                <span>P.O BOX 61908 Nairobi, Kenya</span>
            </p>
        </div>
    </div>
    <div style="text-align:center; margin-top:6px;">
        <p style="font-size:15px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px;">Full Membership Report</p>
        <p style="font-size:11px; color:#666; margin-top:3px;">{{ now()->format('d M Y') }}</p>
    </div>
</div>

{{-- Grouped sections --}}
@foreach($grouped as $officeName => $group)
<div class="report-card bg-white rounded-xl shadow mb-6 overflow-hidden">
    {{-- Section header --}}
    <div class="px-6 py-3 flex items-center justify-between" style="background:#0a1f44;">
        <h3 class="text-white font-bold text-base">
            @php
                $icon = match($officeName) {
                    'Presbyter'  => 'fa-star',
                    'Pastor'     => 'fa-cross',
                    'Elder'      => 'fa-user-tie',
                    'Deacon'     => 'fa-hands-helping',
                    'Deaconess'  => 'fa-hands-helping',
                    default      => 'fa-user',
                };
            @endphp
            <i class="fas {{ $icon }} mr-2" style="color:#f0a500;"></i>
            {{ $officeName }}s
        </h3>
        <span class="text-sm font-semibold px-2 py-0.5 rounded-full" style="background:#f0a500;color:#0a1f44;">
            {{ $group->count() }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">S/No</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Full Name</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Gender</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Marital Status</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Phone</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">County</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Department</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Home Cell</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Deacon/Deaconess</th>
                    <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($group as $i => $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 font-medium text-gray-800">
                        {{ $m->name }} {{ $m->middle_name }} {{ $m->last_name }}
                    </td>
                    <td class="px-4 py-2 text-gray-600 capitalize">{{ $m->gender ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600 capitalize">{{ $m->marital_status ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $m->phone ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $m->county ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $m->department ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $m->home_cell ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $m->deacon_name ?: 'â€”' }}</td>
                    <td class="px-4 py-2 text-gray-500 text-xs">
                        {{ $m->membership_date ? $m->membership_date->format('M Y') : 'â€”' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endforeach

{{-- Summary footer --}}
<div class="bg-white rounded-xl shadow p-4 flex flex-wrap gap-4 text-sm no-print">
    @foreach(['Presbyter','Pastor','Elder','Deacon','Deaconess','Member'] as $o)
        @php $count = $grouped->get($o)?->count() ?? 0; @endphp
        @if($count)
        <div class="flex items-center gap-2 px-3 py-1 rounded-full" style="background:#f1f5f9;">
            <span class="font-semibold" style="color:#0a1f44;">{{ $o }}s:</span>
            <span class="font-bold" style="color:#c0392b;">{{ $count }}</span>
        </div>
        @endif
    @endforeach
</div>

@endsection






