@extends('layouts.admin')

@section('title', 'By Department Report')
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

{{-- Top bar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 report-actions no-print">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Department Report</h1>
        <p class="text-gray-500 text-sm mt-1">Members grouped by their ministry department</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        {{-- Reports dropdown --}}
        <div class="relative group">
            <button class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-bar"></i> Reports <i class="fas fa-chevron-down text-xs"></i>
            </button>
            <div class="absolute right-0 mt-1 w-64 bg-white rounded-lg shadow-lg border border-gray-100 z-50 hidden group-hover:block">
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
                <a href="{{ route('admin.reports.by-department') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold hover:bg-gray-50" style="color:#0a1f44;">
                    <i class="fas fa-layer-group w-4"></i> By Department
                </a>
            </div>
        </div>

        @if($selectedDept)
        <a href="{{ route('admin.reports.by-department', ['department' => $selectedDept, 'export' => 'excel']) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#1d6f42;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <button onclick="
            @if($selectedDept === 'all')
                openPrintDialogMulti('.report-card table', 'Department Report', 'All Departments Overview')
            @else
                openPrintDialog('#report-table', 'Department Report', '{{ $selectedDept }}')
            @endif
            "
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print / PDF
        </button>
        @endif
    </div>
</div>

{{-- Department selector --}}
<div class="bg-white rounded-xl shadow p-6 mb-6 no-print">
    <form method="GET" action="{{ route('admin.reports.by-department') }}" class="flex flex-col sm:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-layer-group mr-1" style="color:#0a1f44;"></i>
                Select Department
            </label>
            <select name="department" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option value="">— Choose a department —</option>
                <option value="all" {{ $selectedDept === 'all' ? 'selected' : '' }}>All Departments (Overview)</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ $selectedDept === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 rounded-lg text-sm font-semibold text-white flex items-center gap-2"
                style="background:#c0392b;">
            <i class="fas fa-search"></i> Generate Report
        </button>
    </form>
    @if($departments->isEmpty())
        <p class="text-gray-400 text-sm mt-3">
            <i class="fas fa-info-circle mr-1"></i>No departments found. Add department info to members first.
        </p>
    @endif
</div>

{{-- ── Single department view ── --}}
@if($selectedDept && $selectedDept !== 'all')

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
            <p style="font-size:15px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px;">Department Report</p>
            <p style="font-size:11px; color:#666; margin-top:3px;">{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    <div class="report-card bg-white rounded-xl shadow overflow-hidden mb-6">
        {{-- Department header --}}
        <div class="px-6 py-4 flex items-center gap-4" style="background:#0a1f44;">
            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-xl flex-shrink-0"
                 style="background:#c0392b;">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <p class="text-white font-bold text-lg">{{ $selectedDept }}</p>
                <p class="text-yellow-400 text-sm">Ministry Department</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-3xl font-bold text-white">{{ $members->count() }}</p>
                <p class="text-gray-300 text-xs">{{ $members->count() === 1 ? 'Member' : 'Members' }}</p>
            </div>
        </div>

        @if($members->count())
        <div class="overflow-x-auto">
            <table id="report-table" class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">S/No</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Full Name</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Gender</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Marital Status</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Office</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Phone</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">County</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Home Cell</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">All Departments</th>
                        <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Deacon / Deaconess</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($members as $i => $m)
                    @php
                        $allDepts = collect([$m->department, $m->department2, $m->department3])->filter()->values();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ trim($m->name . ' ' . $m->middle_name . ' ' . $m->last_name) }}
                        </td>
                        <td class="px-4 py-3 capitalize text-gray-600">{{ $m->gender ?: '—' }}</td>
                        <td class="px-4 py-3 capitalize text-gray-600">{{ $m->marital_status ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($m->office)
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold" style="background:#e8f0fe;color:#0a1f44;">
                                    {{ ucfirst($m->office) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Member</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $m->phone ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $m->county ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $m->home_cell ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @foreach($allDepts as $d)
                                <span class="inline-block text-xs px-2 py-0.5 rounded-full mr-1 mb-0.5" style="background:#f0fdf4;color:#166534;">{{ $d }}</span>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $m->deacon_name ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-10 text-center text-gray-400">
            <i class="fas fa-users text-4xl mb-3 block"></i>
            <p class="font-semibold">No members found in <strong>{{ $selectedDept }}</strong></p>
        </div>
        @endif
    </div>

{{-- ── All departments overview ── --}}
@elseif($selectedDept === 'all' && $allGrouped)

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
            <p style="font-size:15px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px;">Department Report — All Departments</p>
            <p style="font-size:11px; color:#666; margin-top:3px;">{{ now()->format('d M Y') }}</p>
        </div>
    </div>

    {{-- Summary stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6 no-print">
        @foreach($allGrouped as $dept => $deptMembers)
        <div class="bg-white rounded-xl shadow p-4 border-l-4" style="border-color:#0a1f44;">
            <p class="text-2xl font-bold" style="color:#0a1f44;">{{ $deptMembers->count() }}</p>
            <p class="text-xs text-gray-500 mt-1 font-semibold">{{ $dept }}</p>
        </div>
        @endforeach
    </div>

    @foreach($allGrouped as $dept => $deptMembers)
    <div class="report-card bg-white rounded-xl shadow mb-5 overflow-hidden">
        <div class="px-6 py-3 flex items-center justify-between" style="background:#0a1f44;">
            <h3 class="text-white font-bold text-base">
                <i class="fas fa-layer-group mr-2" style="color:#f0a500;"></i>{{ $dept }}
            </h3>
            <span class="text-sm font-bold px-2 py-0.5 rounded-full" style="background:#f0a500;color:#0a1f44;">
                {{ $deptMembers->count() }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Full Name</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Gender</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Office</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Phone</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500 font-semibold">Home Cell</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($deptMembers as $i => $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800">{{ trim($m->name . ' ' . $m->middle_name . ' ' . $m->last_name) }}</td>
                        <td class="px-4 py-2 capitalize text-gray-600">{{ $m->gender ?: '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->office ? ucfirst($m->office) : 'Member' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->phone ?: '—' }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $m->home_cell ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

@endif

@endsection




