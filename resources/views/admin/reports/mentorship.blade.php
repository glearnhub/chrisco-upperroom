@extends('layouts.admin')

@section('title', 'Mentorship Report')
@section('page-title', 'Reports')

@push('styles')
<style>
@media print {
    .sidebar, header, .report-actions, .no-print, form { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    .report-card { box-shadow: none !important; border: 1px solid #ddd; }
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

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 report-actions no-print">
    <div>
        <h1 class="text-2xl font-bold" style="color: #0a1f44;">Mentorship Report</h1>
        <p class="text-gray-500 text-sm mt-1">Members assigned to a Deacon / Deaconess</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <div class="relative group">
            <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div class="absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50 hidden group-hover:block">
                <a href="{{ route('admin.reports.membership') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-users w-4"></i> Full Membership
                </a>
                <a href="{{ route('admin.reports.leaders') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    <i class="fas fa-crown w-4"></i> Leaders Report
                </a>
                <a href="{{ route('admin.reports.mentorship') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold hover:bg-gray-50" style="color:#0a1f44;">
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
        @if($selectedMentor && $mentees->count())
        <a href="{{ route('admin.reports.mentorship', ['mentor' => $selectedMentor, 'export' => 'excel']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#1d6f42;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <button onclick="openPrintDialog('#report-table', 'Mentorship Report', 'Deacon / Deaconess: {{ $selectedMentor }} &mdash; {{ $mentees->count() }} members')"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print / PDF
        </button>
        @endif
    </div>
</div>

{{-- Mentor selector --}}
<div class="bg-white rounded-xl shadow p-6 mb-6 no-print">
    <form method="GET" action="{{ route('admin.reports.mentorship') }}" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-user-shield mr-1" style="color:#0a1f44;"></i>
                Select Deacon / Deaconess
            </label>
            <select name="mentor" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">â€” Choose a Deacon or Deaconess â€”</option>
                @foreach($mentorNames as $name)
                    <option value="{{ $name }}" {{ $selectedMentor === $name ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 rounded-lg text-sm font-semibold text-white flex items-center gap-2"
                style="background:#c0392b;">
            <i class="fas fa-search"></i> Generate Report
        </button>
    </form>
    @if($mentorNames->isEmpty())
        <p class="text-gray-400 text-sm mt-3"><i class="fas fa-info-circle mr-1"></i>No deacon/deaconess assignments found. Assign members to a Deacon or Deaconess first.</p>
    @endif
</div>

@if($selectedMentor)

    {{-- Print header --}}
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
            <p style="font-size:15px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px;">Deacon / Deaconess Mentorship Report</p>
            <p style="font-size:11px; color:#666; margin-top:3px;">{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Mentor header card --}}
    <div class="report-card bg-white rounded-xl shadow mb-4 overflow-hidden">
        <div class="px-6 py-4 flex items-center gap-4" style="background:#0a1f44;">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                 style="background:#c0392b;">
                {{ strtoupper(substr($selectedMentor, 0, 1)) }}
            </div>
            <div>
                <p class="text-white font-bold text-lg">{{ $selectedMentor }}</p>
                <p class="text-yellow-400 text-sm">Deacon / Deaconess &mdash; Mentorship Report</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-3xl font-bold text-white">{{ $mentees->count() }}</p>
                <p class="text-gray-300 text-xs">Members Assigned</p>
            </div>
        </div>

        @if($mentees->count())
        <div class="overflow-x-auto">
            <table id="report-table" class="w-full text-sm">
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
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Committed?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($mentees as $i => $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800">
                            {{ $m->name }} {{ $m->middle_name }} {{ $m->last_name }}
                        </td>
                        <td class="px-4 py-2 capitalize text-gray-600">{{ $m->gender ?: 'â€”' }}</td>
                        <td class="px-4 py-2 capitalize text-gray-600">{{ $m->marital_status ?: 'â€”' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->phone ?: 'â€”' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->county ?: 'â€”' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->department ?: 'â€”' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->home_cell ?: 'â€”' }}</td>
                        <td class="px-4 py-2">
                            @if($m->is_committed_member)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Yes</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">No</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-10 text-center text-gray-400">
            <i class="fas fa-users-slash text-4xl mb-3"></i>
            <p class="font-semibold">No members assigned to <strong>{{ $selectedMentor }}</strong></p>
            <p class="text-sm mt-1">Members can be assigned via the Members edit form.</p>
        </div>
        @endif
    </div>

@endif

@endsection





