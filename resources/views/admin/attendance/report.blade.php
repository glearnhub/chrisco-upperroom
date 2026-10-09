@extends('layouts.admin')
@section('title', 'Attendance Report — ' . $monthName)

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    .sidebar, header { display: none !important; }
    body { background: white !important; }
    .print-header { display: block !important; }
    .section-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .page-break { page-break-before: always; }
    .section-body { display: block !important; }
    /* Selective print classes */
    body.print-active   .section-card-inactive,
    body.print-active   .section-card-irregular  { display: none !important; }
    body.print-inactive .section-card-active,
    body.print-inactive .section-card-irregular  { display: none !important; }
    body.print-irregular .section-card-active,
    body.print-irregular .section-card-inactive  { display: none !important; }
    /* Hide stats and sessions when printing a specific group */
    body.print-active .print-hide-on-group,
    body.print-inactive .print-hide-on-group,
    body.print-irregular .print-hide-on-group   { display: none !important; }
}
.print-header { display: none; }
/* Print dropdown */
.print-dropdown { position: relative; display: inline-block; }
.print-dropdown-menu {
    display: none; position: absolute; right: 0; top: calc(100% + 4px);
    background: white; border: 1px solid #e5e7eb; border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,.10); min-width: 200px; z-index: 50; overflow: hidden;
}
.print-dropdown-menu.open { display: block; }
.print-dropdown-menu button {
    display: flex; align-items: center; gap-10px; width: 100%;
    padding: 9px 14px; text-align: left; font-size: 13px; font-weight: 500;
    color: #374151; background: none; border: none; cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
}
.print-dropdown-menu button:last-child { border-bottom: none; }
.print-dropdown-menu button:hover { background: #f9fafb; }
.print-dropdown-menu .dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-right: 8px;
}

/* Accordion */
.section-header { cursor: pointer; user-select: none; }
.section-header:hover { filter: brightness(0.97); }
.section-chevron { transition: transform .25s ease; flex-shrink: 0; }
.section-body { overflow: hidden; transition: max-height .3s ease, opacity .25s ease; max-height: 0; opacity: 0; }
.section-body.open { max-height: 9999px; opacity: 1; }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6">

    {{-- Print header --}}
    <div class="print-header mb-6">
        <div style="display:flex;align-items:center;gap:20px;border-bottom:3px solid #0a1f44;padding-bottom:14px;margin-bottom:10px;">
            <img src="{{ asset('images/logo.png') }}" alt="Chrisco Upper Room" style="height:60px;width:auto;object-fit:contain;flex-shrink:0;">
            <div>
                <h1 style="font-size:18px;font-weight:900;color:#0a1f44;line-height:1.1;">Chrisco Upper Room Fellowship</h1>
                <p style="font-size:10px;color:#c0392b;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-top:2px;">Where God Dwells</p>
            </div>
        </div>
        <div style="text-align:center;margin-top:6px;">
            <p style="font-size:15px;font-weight:800;color:#0a1f44;text-transform:uppercase;">Sunday Attendance Report — {{ $monthName }}</p>
            <p style="font-size:10px;color:#888;margin-top:2px;">Printed: {{ now()->format('d M Y, g:i A') }}</p>
        </div>
    </div>

    {{-- Top bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">Sunday Attendance Report</h1>
            <p class="text-sm text-gray-500 mt-0.5">Member activity by month</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.attendance.index') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700">
                <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Back</span>
            </a>
            <div class="print-dropdown" id="printDropdown">
                <button onclick="togglePrintMenu(event)"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700">
                    <i class="fas fa-print"></i>
                    <span class="hidden sm:inline">Print</span>
                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                </button>
                <div class="print-dropdown-menu" id="printMenu">
                    <button onclick="printSection('all')">
                        <span class="dot" style="background:#6b7280;"></span> Print All
                    </button>
                    <button onclick="printSection('active')">
                        <span class="dot" style="background:#16a34a;"></span> Active Members Only
                    </button>
                    <button onclick="printSection('inactive')">
                        <span class="dot" style="background:#dc2626;"></span> Inactive Members Only
                    </button>
                    <button onclick="printSection('irregular')">
                        <span class="dot" style="background:#d97706;"></span> Irregular Members Only
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Month selector --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 no-print">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Month</label>
                <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Year</label>
                <select name="year" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 rounded-lg text-sm font-semibold text-white" style="background:#0a1f44;">
                <i class="fas fa-chart-bar mr-1"></i> Generate Report
            </button>
        </form>
    </div>

    {{-- Summary stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 print-hide-on-group">
        @php
        $totalMembers    = count($members);
        $activeCount     = count($activeMembers);
        $inactiveCount   = count($inactiveMembers);
        $irregularCount  = count($irregularMembers);
        $activePct       = $totalMembers > 0 ? round($activeCount / $totalMembers * 100) : 0;
        $inactivePct     = $totalMembers > 0 ? round($inactiveCount / $totalMembers * 100) : 0;
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-extrabold" style="color:#0a1f44;">{{ $totalSundays }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Sundays in Month</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-extrabold text-blue-600">{{ count($sessions) }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Sessions Held</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-extrabold text-gray-700">{{ $totalMembers }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Members</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center" style="border-color:#dcfce7;">
            <p class="text-2xl font-extrabold text-green-600">{{ $activeCount }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Active <span class="text-green-600 font-semibold">({{ $activePct }}%)</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center" style="border-color:#fee2e2;">
            <p class="text-2xl font-extrabold text-red-600">{{ $inactiveCount }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Inactive <span class="text-red-600 font-semibold">({{ $inactivePct }}%)</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center" style="border-color:#fef3c7;">
            <p class="text-2xl font-extrabold text-yellow-600">{{ $irregularCount }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Irregular</p>
        </div>
    </div>

    {{-- Activity legend --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 no-print">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Classification Rules — {{ $monthName }}</p>
        <div class="flex flex-wrap gap-4 text-sm">
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                <strong class="text-green-700">Active</strong> — attended 3+ Sundays out of {{ $totalSundays }}
            </span>
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                <strong class="text-red-700">Inactive</strong> — missed 3+ Sundays (attended {{ $totalSundays > 3 ? '0–'.($totalSundays - 3) : '0' }})
            </span>
            <span class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
                <strong class="text-yellow-700">Irregular</strong> — attended 1–2 but missed fewer than 3
            </span>
        </div>
    </div>

    {{-- Sessions breakdown --}}
    @if($sessions->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6 section-card print-hide-on-group">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800"><i class="fas fa-calendar-check mr-2 text-blue-600"></i>Sessions Held — {{ $monthName }}</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#0a1f44;">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Sunday</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Service</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-white">Check-ins</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($sessions as $sess)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold" style="color:#0a1f44;">
                        {{ $sess->service_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">
                        Sunday {{ array_search($sess->service_date->toDateString(), $sundays) !== false ? '#'.(array_search($sess->service_date->toDateString(), $sundays)+1) : '' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $sess->label_display }}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-700">{{ $sess->attendances_count }}</td>
                    <td class="px-4 py-3">
                        @if($sess->status === 'open')
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Open</span>
                        @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Closed</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                {{-- Sundays with no session --}}
                @foreach($sundays as $sun)
                    @php $hasSession = $sessions->first(fn($s) => $s->service_date->toDateString() === $sun); @endphp
                    @if(!$hasSession)
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ \Carbon\Carbon::parse($sun)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">Sunday #{{ array_search($sun, $sundays) + 1 }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs italic" colspan="3">No session recorded</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400 mb-6 print-hide-on-group">
        <i class="fas fa-calendar-times text-4xl mb-3 block"></i>
        <p class="font-medium">No sessions recorded for {{ $monthName }}</p>
        <p class="text-sm mt-1">Open and run Sunday sessions first, then return here to view the report.</p>
    </div>
    @endif

    {{-- ── ACTIVE MEMBERS ── --}}
    <div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden mb-6 section-card section-card-active" style="border-color:#16a34a;">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap section-header no-print"
             onclick="toggleSection('active')" role="button" aria-expanded="false" aria-controls="body-active">
            <h2 class="font-bold flex items-center gap-2" style="color:#16a34a;">
                <i class="fas fa-check-circle"></i>Active Members
                <span class="text-sm font-semibold text-gray-500">(attended 3+ Sundays)</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700">{{ $activeCount }} members</span>
                <i class="fas fa-chevron-down section-chevron text-gray-400" id="chevron-active"></i>
            </div>
        </div>
        {{-- always-visible header for print --}}
        <div class="px-5 py-4 border-b border-gray-100 hidden print-only" style="display:none;">
            <h2 class="font-bold" style="color:#16a34a;"><i class="fas fa-check-circle mr-2"></i>Active Members <span class="text-sm font-semibold text-gray-500">(attended 3+ Sundays)</span></h2>
        </div>
        <div class="section-body" id="body-active">
        @if($activeCount)
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-green-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wide">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wide">Name</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase tracking-wide">Attended</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-700 uppercase tracking-wide">Missed</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-green-700 uppercase tracking-wide hidden md:table-cell">Sundays Present</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($activeMembers as $i => $m)
                <tr class="hover:bg-green-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800">{{ $m->full_name }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            {{ $m->sundays_attended }} / {{ $totalSundays }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs text-gray-400">{{ $m->sundays_missed }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">
                        {{ implode(', ', array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $m->attended_dates)) ?: '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="px-5 py-8 text-center text-gray-400 text-sm">No active members recorded for this month.</div>
        @endif
        </div>
    </div>

    {{-- ── INACTIVE MEMBERS ── --}}
    <div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden mb-6 section-card section-card-inactive page-break" style="border-color:#dc2626;">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap section-header no-print"
             onclick="toggleSection('inactive')" role="button" aria-expanded="false" aria-controls="body-inactive">
            <h2 class="font-bold flex items-center gap-2" style="color:#dc2626;">
                <i class="fas fa-exclamation-circle"></i>Inactive Members
                <span class="text-sm font-semibold text-gray-500">(missed 3+ Sundays)</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">{{ $inactiveCount }} members</span>
                <i class="fas fa-chevron-down section-chevron text-gray-400" id="chevron-inactive"></i>
            </div>
        </div>
        <div class="section-body" id="body-inactive">
        @if($inactiveCount)
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-red-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide hidden lg:table-cell">Department</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wide">Attended</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wide">Missed</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide hidden md:table-cell">Sundays Present</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide no-print">Follow-Up</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php $fuDataPrinted = []; @endphp
                @foreach($inactiveMembers as $i => $m)
                @php $fu = $followups[$m->id] ?? null; @endphp
                <tr class="hover:bg-red-50" id="row-{{ $m->id }}">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $m->full_name }}</div>
                        @if($m->office)<div class="text-xs text-gray-400">{{ $m->office }}</div>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->phone ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">{{ $m->department ?: '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                            {{ $m->sundays_attended }} / {{ $totalSundays }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-200 text-red-800">
                            {{ $m->sundays_missed }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">
                        {{ count($m->attended_dates) ? implode(', ', array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $m->attended_dates)) : 'None' }}
                    </td>
                    <td class="px-4 py-3 no-print" style="min-width:160px;">
                        {{-- Follow-up status badge (shown when recorded) --}}
                        <div id="fu-badge-{{ $m->id }}" class="{{ $fu ? '' : 'hidden' }}">
                            <span id="fu-label-{{ $m->id }}"
                                  class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 cursor-pointer"
                                  onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')">
                                {{ $fu ? $fu->reason_label : '' }}
                            </span>
                            @if($fu && $fu->transferred_to)
                            <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5">→ {{ $fu->transferred_to }}</div>
                            @else
                            <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5 hidden"></div>
                            @endif
                        </div>
                        {{-- Record button (shown when no follow-up yet) --}}
                        <button id="fu-btn-{{ $m->id }}"
                                onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')"
                                class="text-xs px-2 py-1 rounded border font-semibold {{ $fu ? 'hidden' : '' }}"
                                style="border-color:#dc2626;color:#dc2626;">
                            <i class="fas fa-plus mr-1"></i>Record Reason
                        </button>
                    </td>
                </tr>
                @php $fuDataPrinted[$m->id] = true; @endphp
                <script>
                window._fuData = window._fuData || {};
                window._fuData[{{ $m->id }}] = {
                    reason: '{{ $fu?->reason ?? '' }}',
                    transferred_to: '{{ addslashes($fu?->transferred_to ?? '') }}',
                    notes: '{{ addslashes($fu?->notes ?? '') }}',
                };
                </script>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="px-5 py-8 text-center text-gray-400 text-sm">
            <i class="fas fa-check-double mr-2 text-green-400"></i>No inactive members this month.
        </div>
        @endif
        </div>{{-- /section-body inactive --}}
    </div>

    {{-- ── FOLLOW-UP MODAL ── --}}
    <div id="fu-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background:rgba(0,0,0,.45);">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-base" style="color:#0a1f44;">
                        <i class="fas fa-clipboard-list mr-2"></i>Follow-Up Reason
                    </h3>
                    <button onclick="closeFollowupModal()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
                </div>
                <div class="px-5 py-4">
                    <p class="text-sm text-gray-500 mb-4">Recording reason for: <strong id="fu-modal-name" class="text-gray-800"></strong></p>
                    <input type="hidden" id="fu-modal-uid">

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Reason for Missing</label>
                        <select id="fu-modal-reason"
                                onchange="document.getElementById('fu-church-wrap').style.display = this.value === 'transferred' ? 'block' : 'none'"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                            <option value="">— Select reason —</option>
                            @foreach($reasonLabels as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="fu-church-wrap" class="mb-4 hidden">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Church Transferred To</label>
                        <input type="text" id="fu-modal-church" placeholder="e.g. Chrisco Upper Room Westlands"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                        <textarea id="fu-modal-notes" rows="2" placeholder="Any additional details…"
                                  class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                    </div>

                    <div id="fu-modal-err" class="hidden mb-3 text-sm text-red-600 font-semibold"></div>
                </div>
                <div class="px-5 pb-4 flex items-center gap-3">
                    <button onclick="saveFollowup()"
                            class="flex-1 py-2 rounded-lg text-sm font-bold text-white"
                            style="background:#0a1f44;">
                        <i class="fas fa-save mr-1"></i> Save
                    </button>
                    <button id="fu-modal-clear-btn" onclick="clearFollowup()"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 hidden">
                        <i class="fas fa-trash mr-1"></i> Clear
                    </button>
                    <button onclick="closeFollowupModal()"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── IRREGULAR MEMBERS ── --}}
    @if($irregularCount)
    <div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden mb-6 section-card section-card-irregular" style="border-color:#d97706;">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap section-header no-print"
             onclick="toggleSection('irregular')" role="button" aria-expanded="false" aria-controls="body-irregular">
            <h2 class="font-bold flex items-center gap-2" style="color:#d97706;">
                <i class="fas fa-exclamation-triangle"></i>Irregular / At Risk
                <span class="text-sm font-semibold text-gray-500">(attended 1–2, missed 1–2)</span>
            </h2>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700">{{ $irregularCount }} members</span>
                <i class="fas fa-chevron-down section-chevron text-gray-400" id="chevron-irregular"></i>
            </div>
        </div>
        <div class="section-body" id="body-irregular">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-yellow-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide hidden lg:table-cell">Department</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wide">Attended</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wide">Missed</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide hidden md:table-cell">Sundays Present</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide no-print">Follow-Up</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($irregularMembers as $i => $m)
                @php $fu = $followups[$m->id] ?? null; @endphp
                <tr class="hover:bg-yellow-50" id="row-{{ $m->id }}">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-gray-800">{{ $m->full_name }}</div>
                        @if($m->office)<div class="text-xs text-gray-400">{{ $m->office }}</div>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->phone ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">{{ $m->department ?: '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                            {{ $m->sundays_attended }} / {{ $totalSundays }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $m->sundays_missed }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500 hidden md:table-cell">
                        {{ count($m->attended_dates) ? implode(', ', array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $m->attended_dates)) : 'None' }}
                    </td>
                    <td class="px-4 py-3 no-print" style="min-width:160px;">
                        <div id="fu-badge-{{ $m->id }}" class="{{ $fu ? '' : 'hidden' }}">
                            <span id="fu-label-{{ $m->id }}"
                                  class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 cursor-pointer"
                                  onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')">
                                {{ $fu ? $fu->reason_label : '' }}
                            </span>
                            @if($fu && $fu->transferred_to)
                            <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5">→ {{ $fu->transferred_to }}</div>
                            @else
                            <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5 hidden"></div>
                            @endif
                        </div>
                        <button id="fu-btn-{{ $m->id }}"
                                onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')"
                                class="text-xs px-2 py-1 rounded border font-semibold {{ $fu ? 'hidden' : '' }}"
                                style="border-color:#d97706;color:#d97706;">
                            <i class="fas fa-plus mr-1"></i>Record Reason
                        </button>
                    </td>
                </tr>
                @if(!isset($fuDataPrinted[$m->id]))
                <script>
                window._fuData = window._fuData || {};
                window._fuData[{{ $m->id }}] = window._fuData[{{ $m->id }}] || {
                    reason: '{{ $fu?->reason ?? '' }}',
                    transferred_to: '{{ addslashes($fu?->transferred_to ?? '') }}',
                    notes: '{{ addslashes($fu?->notes ?? '') }}',
                };
                </script>
                @php $fuDataPrinted[$m->id] = true; @endphp
                @endif
                @endforeach
            </tbody>
        </table>
        </div>
        </div>{{-- /section-body irregular --}}
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
// ── Accordion ──────────────────────────────────────────────────────────────
const SECTIONS = ['active', 'inactive', 'irregular'];

function toggleSection(id) {
    const isOpen = document.getElementById('body-' + id).classList.contains('open');
    // collapse all
    SECTIONS.forEach(s => {
        document.getElementById('body-' + s)?.classList.remove('open');
        const ch = document.getElementById('chevron-' + s);
        if (ch) ch.style.transform = '';
        const hdr = document.querySelector('[aria-controls="body-' + s + '"]');
        if (hdr) hdr.setAttribute('aria-expanded', 'false');
    });
    // expand clicked one only if it was closed
    if (!isOpen) {
        document.getElementById('body-' + id).classList.add('open');
        const ch = document.getElementById('chevron-' + id);
        if (ch) ch.style.transform = 'rotate(180deg)';
        const hdr = document.querySelector('[aria-controls="body-' + id + '"]');
        if (hdr) hdr.setAttribute('aria-expanded', 'true');
    }
}
// all collapsed on load — nothing to init
// ─────────────────────────────────────────────────────────────────────────

const FU_SAVE_URL   = '{{ route('admin.attendance.followup.save') }}';
const FU_DELETE_URL = '{{ route('admin.attendance.followup.delete') }}';
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
const REPORT_YEAR   = {{ $year }};
const REPORT_MONTH  = {{ $month }};

let _fuModalUid = null;

function openFollowupModal(uid, name) {
    _fuModalUid = uid;
    document.body.style.overflow = 'hidden';
    document.getElementById('fu-modal-name').textContent = name;
    document.getElementById('fu-modal-uid').value = uid;
    document.getElementById('fu-modal-err').classList.add('hidden');

    const existing = (window._fuData || {})[uid] || {};
    const reason = existing.reason || '';
    document.getElementById('fu-modal-reason').value = reason;
    document.getElementById('fu-modal-church').value = existing.transferred_to || '';
    document.getElementById('fu-modal-notes').value  = existing.notes || '';
    document.getElementById('fu-church-wrap').style.display = reason === 'transferred' ? 'block' : 'none';
    document.getElementById('fu-modal-clear-btn').classList.toggle('hidden', !reason);

    document.getElementById('fu-modal').classList.remove('hidden');
}

function closeFollowupModal() {
    document.getElementById('fu-modal').classList.add('hidden');
    document.body.style.overflow = '';
    _fuModalUid = null;
}

async function saveFollowup() {
    const uid    = _fuModalUid;
    const reason = document.getElementById('fu-modal-reason').value;
    const church = document.getElementById('fu-modal-church').value.trim();
    const notes  = document.getElementById('fu-modal-notes').value.trim();
    const errEl  = document.getElementById('fu-modal-err');

    if (!reason) { errEl.textContent = 'Please select a reason.'; errEl.classList.remove('hidden'); return; }
    if (reason === 'transferred' && !church) { errEl.textContent = 'Please enter the church name.'; errEl.classList.remove('hidden'); return; }
    errEl.classList.add('hidden');

    try {
        const res = await fetch(FU_SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: uid, year: REPORT_YEAR, month: REPORT_MONTH, reason, transferred_to: church, notes }),
        });
        const d = await res.json();
        if (!d.success) throw new Error('Server error');

        // Update local cache
        window._fuData = window._fuData || {};
        window._fuData[uid] = { reason, transferred_to: church, notes };

        // Update the row badge
        document.getElementById('fu-label-' + uid).textContent = d.reason_label;
        const churchEl = document.getElementById('fu-church-' + uid);
        if (d.transferred_to) { churchEl.textContent = '→ ' + d.transferred_to; churchEl.classList.remove('hidden'); }
        else { churchEl.textContent = ''; churchEl.classList.add('hidden'); }
        document.getElementById('fu-badge-' + uid).classList.remove('hidden');
        document.getElementById('fu-btn-' + uid).classList.add('hidden');

        closeFollowupModal();
    } catch(e) {
        errEl.textContent = 'Failed to save. Please try again.';
        errEl.classList.remove('hidden');
    }
}

async function clearFollowup() {
    const uid = _fuModalUid;
    try {
        await fetch(FU_DELETE_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: uid, year: REPORT_YEAR, month: REPORT_MONTH }),
        });
        window._fuData = window._fuData || {};
        window._fuData[uid] = { reason: '', transferred_to: '', notes: '' };
        document.getElementById('fu-badge-' + uid).classList.add('hidden');
        document.getElementById('fu-btn-' + uid).classList.remove('hidden');
        closeFollowupModal();
    } catch(e) {}
}

// Close modal on backdrop click
document.getElementById('fu-modal').addEventListener('click', function(e) {
    if (e.target === this) closeFollowupModal();
});

// ── Print dropdown ─────────────────────────────────────────────────────────
function togglePrintMenu(e) {
    e.stopPropagation();
    document.getElementById('printMenu').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!document.getElementById('printDropdown').contains(e.target)) {
        document.getElementById('printMenu').classList.remove('open');
    }
});

function printSection(type) {
    document.getElementById('printMenu').classList.remove('open');
    // Expand all sections so they are visible on print
    ['active', 'inactive', 'irregular'].forEach(s => {
        document.getElementById('body-' + s)?.classList.add('open');
    });
    // Apply body class to filter sections
    document.body.classList.remove('print-active', 'print-inactive', 'print-irregular');
    if (type !== 'all') document.body.classList.add('print-' + type);
    // Update print header label
    const labels = { all: 'Full Report', active: 'Active Members', inactive: 'Inactive Members', irregular: 'Irregular Members' };
    document.querySelector('.print-header p[style*="font-size:15px"]').textContent =
        'Sunday Attendance Report — {{ $monthName }} — ' + labels[type];
    window.print();
    // Clean up after print dialog closes
    setTimeout(() => {
        document.body.classList.remove('print-active', 'print-inactive', 'print-irregular');
        document.querySelector('.print-header p[style*="font-size:15px"]').textContent =
            'Sunday Attendance Report — {{ $monthName }}';
    }, 1000);
}
</script>
@endpush
