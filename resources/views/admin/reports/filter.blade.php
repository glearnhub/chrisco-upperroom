@extends('layouts.admin')

@section('title', $title)
@section('page-title', $title)

@push('styles')
<style>
@media print {
    .sidebar, header, .report-actions, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    .report-card { box-shadow: none !important; border: 1px solid #ddd; }
    .print-header { display: block !important; }
}
@page {
    margin: 1.5cm;
}
.print-header { display: none; }
</style>
@endpush

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 report-actions no-print">
    <div>
        <h1 class="text-2xl font-bold" style="color: #0a1f44;">{{ $title }}</h1>
        <p class="text-gray-500 text-sm mt-1">{{ $subtitle }} &mdash; {{ $members->count() }} total</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        {{-- Excel Export --}}
        <a href="{{ request()->fullUrlWithQuery(['export' => '1']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#15803d;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        {{-- Reports dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div class="absolute right-0 mt-1 w-60 bg-white rounded-lg shadow-lg border border-gray-100 z-50 hidden group-hover:block">
                <a href="{{ route('admin.reports.membership') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
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
                <div class="border-t border-gray-100 my-1"></div>
                <a href="{{ route('admin.reports.committed') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.committed') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.committed') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-certificate w-4" style="color:#16a34a;"></i> Committed Members
                </a>
                <a href="{{ route('admin.reports.in-commitment') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.in-commitment') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.in-commitment') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-book-open w-4" style="color:#f0a500;"></i> In Commitment Class
                </a>
                <a href="{{ route('admin.reports.young-converts') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.young-converts') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.young-converts') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-seedling w-4" style="color:#0284c7;"></i> Young Converts
                </a>
                <a href="{{ route('admin.reports.not-baptised') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.not-baptised') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.not-baptised') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-water w-4" style="color:#c0392b;"></i> Not Baptised
                </a>
                <a href="{{ route('admin.reports.married') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.married') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.married') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-rings-wedding w-4" style="color:#7c3aed;"></i> Married
                </a>
                <a href="{{ route('admin.reports.pearls') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.pearls') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.pearls') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-gem w-4" style="color:#f0a500;"></i> Pearls Fellowship
                </a>
                <a href="{{ route('admin.reports.singles-youths') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.singles-youths') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.singles-youths') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-users w-4" style="color:#0284c7;"></i> Singles / Youths
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <a href="{{ route('admin.reports.transferred-in') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.transferred-in') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.transferred-in') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-sign-in-alt w-4" style="color:#0a1f44;"></i> Transferred In
                </a>
                <a href="{{ route('admin.reports.transferred-out') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.transferred-out') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.transferred-out') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-sign-out-alt w-4" style="color:#c0392b;"></i> Transferred Out
                </a>
                <a href="{{ route('admin.reports.active-members') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.active-members') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.active-members') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-circle-check w-4" style="color:#16a34a;"></i> Active Members
                </a>
                <a href="{{ route('admin.reports.inactive-members') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-50 {{ request()->routeIs('admin.reports.inactive-members') ? 'font-semibold' : 'text-gray-600' }}" style="{{ request()->routeIs('admin.reports.inactive-members') ? 'color:#0a1f44;' : '' }}">
                    <i class="fas fa-circle-xmark w-4" style="color:#f0a500;"></i> Inactive Members
                </a>
            </div>
        </div>
        <button onclick="openPrintDialogMulti('.report-card table', '{{ addslashes($title) }}', '{{ addslashes($subtitle) }} — {{ $members->count() }} members')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print / PDF
        </button>
    </div>
</div>

{{-- Print header --}}
<div class="print-header mb-6">
    <div style="display:flex; align-items:center; gap:20px; border-bottom:3px solid #0a1f44; padding-bottom:14px; margin-bottom:10px;">
        <img src="{{ asset('images/logo.png') }}" alt="Chrisco Upper Room" style="height:70px; width:auto; object-fit:contain; flex-shrink:0;">
        <div>
            <h1 style="font-size:20px; font-weight:900; color:#0a1f44; line-height:1.1;">Chrisco Upper Room Fellowship</h1>
            <p style="font-size:11px; color:#c0392b; font-weight:700; text-transform:uppercase; letter-spacing:.8px; margin-top:2px;">Where God Dwells</p>
        </div>
    </div>
    <div style="text-align:center; margin-top:6px;">
        <p style="font-size:15px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px;">{{ $title }}</p>
        <p style="font-size:11px; color:#666; margin-top:3px;">{{ now()->format('d M Y') }} &mdash; {{ $members->count() }} members</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-x-auto report-card">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <th class="px-4 py-3">S/No</th>
                <th class="px-4 py-3">Full Name</th>
                <th class="px-4 py-3">Gender</th>
                <th class="px-4 py-3">Marital Status</th>
                <th class="px-4 py-3">Phone</th>
                <th class="px-4 py-3">County</th>
                <th class="px-4 py-3">Department</th>
                <th class="px-4 py-3">Home Cell</th>
                <th class="px-4 py-3">Deacon/Deaconess</th>
                <th class="px-4 py-3">Joined</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($members as $i => $m)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-4 py-3 text-gray-400">{{ $i + 1 }}</td>
                <td class="px-4 py-3 font-semibold" style="color:#0a1f44;">{{ $m->full_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->gender ? ucfirst($m->gender) : '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->marital_status ? ucfirst($m->marital_status) : '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->phone ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->county ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->department ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->home_cell ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $m->deacon_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $m->membership_date ? $m->membership_date->format('M Y') : '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-4 py-10 text-center text-gray-400">No members found in this category.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
