@extends('layouts.admin')

@section('title', 'Event Report — ' . $event->title)
@section('page-title', 'Event Report')

@push('styles')
<style>
    @media print {
        /* Hide entire admin chrome */
        .sidebar,
        #admin-sidebar,
        header,
        .no-print { display: none !important; }

        /* Remove sidebar offset so content fills the page */
        .main-content { margin-left: 0 !important; padding: 0 !important; }
        main { padding: 0 !important; margin: 0 !important; }

        /* Show print-only elements */
        .print-header { display: block !important; }

        body { background: white; margin: 0; }
        table { page-break-inside: auto; width: 100%; }
        tr    { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
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

{{-- Print Letterhead --}}
<div class="print-header mb-6">
    <div style="display:flex; align-items:center; gap:20px; border-bottom:3px solid #0a1f44; padding-bottom:14px; margin-bottom:10px;">
        @php $logoPath = public_path('images/logo.png'); @endphp
        @if(file_exists($logoPath))
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:70px; width:auto; object-fit:contain;">
        @endif
        <div>
            <h1 style="font-size:1.3rem; font-weight:800; color:#0a1f44; margin:0;">Chrisco Upper Room Fellowship</h1>
            <p style="color:#c0392b; font-style:italic; margin:2px 0 0;">Where God Dwells</p>
            <p style="font-size:0.75rem; color:#555; margin:4px 0 0;">
                P.O BOX 61908, Nairobi, Kenya &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; info@chrisco-upper-room.org
            </p>
        </div>
    </div>
    <div style="text-align:center; margin-bottom:8px;">
        <h2 style="font-size:1.1rem; font-weight:800; color:#0a1f44; margin:0;">{{ $event->title }} Report</h2>
        <p style="font-size:0.82rem; color:#555; margin:3px 0 0;">{{ $event->location }}</p>
    </div>
    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.78rem; color:#444; padding:6px 0; border-top:1px solid #ddd; border-bottom:1px solid #ddd; margin-bottom:6px;">
        <div>{{ \Carbon\Carbon::parse($event->start_datetime)->format('F d, Y') }}</div>
        <div style="text-align:center;"><strong>Total Registered: {{ $stats['total'] }}</strong></div>
        <div style="text-align:right;"><strong>Total Attended: {{ $registrations->where('attended', true)->count() }}</strong></div>
    </div>
</div>

{{-- Screen Controls --}}
<div class="no-print mb-4 flex items-center justify-between">
    <a href="{{ route('admin.reports.events') }}" class="text-blue-600 text-sm hover:underline">
        <i class="fas fa-arrow-left mr-1"></i>Back to Events
    </a>
    <div class="flex gap-2">
        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white font-semibold" style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="openAttendanceModal()" class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white font-semibold" style="background:#0a1f44;">
            <i class="fas fa-clipboard-check"></i> Mark Attendance
        </button>
        <a href="{{ route('admin.reports.events.show', ['event' => $event->id, 'export' => 'excel', 'filter' => $filter, 'search' => $search]) }}"
           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white font-semibold" style="background:#1a7a3c;">
            <i class="fas fa-file-excel"></i> Excel
        </a>
    </div>
</div>

{{-- Stats Cards --}}
@php $totalAttendedCount = $registrations->where('attended', true)->count(); @endphp
<div class="no-print grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color:#0a1f44;">
        <p class="text-3xl font-bold" style="color:#0a1f44;">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-sm mt-1">Total Participants</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color:#7c3aed;">
        <p class="text-3xl font-bold" style="color:#7c3aed;">{{ $totalAttendedCount }}</p>
        <p class="text-gray-500 text-sm mt-1">Total Attended</p>
        @if($stats['total'] > 0)
            <p class="text-xs mt-1 font-semibold" style="color:#7c3aed;">
                {{ round(($totalAttendedCount / $stats['total']) * 100) }}%
            </p>
        @endif
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color:#c0392b;">
        <p class="text-3xl font-bold" style="color:#c0392b;">{{ $stats['members'] }}</p>
        <p class="text-gray-500 text-sm mt-1">Church Members</p>
    </div>
    <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color:#f0a500;">
        <p class="text-3xl font-bold" style="color:#f0a500;">{{ $stats['visitors'] }}</p>
        <p class="text-gray-500 text-sm mt-1">Visitors</p>
    </div>
</div>

{{-- Filter & Search --}}
<div class="no-print bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('admin.reports.events.show', $event) }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Filter by Category</label>
            <select name="filter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ $filter === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="Name, email, phone..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <button type="submit" class="px-4 py-2 text-sm rounded-lg text-white font-semibold" style="background:#0a1f44;">
            <i class="fas fa-filter mr-1"></i>Apply
        </button>
        <a href="{{ route('admin.reports.events.show', $event) }}" class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">
            Reset
        </a>
    </form>
</div>

{{-- Registration Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-4 border-b" style="background:#0a1f44;">
        <h2 class="text-white font-bold text-lg">
            {{ $filter !== 'all' ? ucfirst($filter) . 's' : 'All Participants' }}
            — {{ $event->title }}
            @if($search)<span class="text-yellow-300 text-sm ml-2">(Search: "{{ $search }}")</span>@endif
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">S/No</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Full Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase no-print">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Attended</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filtered as $i => $reg)
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $reg->full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $reg->phone }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $reg->email ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $reg->member_id ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $reg->member_id ? 'Church Member' : 'Visitor' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 no-print">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $reg->status === 'registered' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($reg->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3" id="attended-cell-{{ $reg->id }}">
                            @if($reg->attended)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">
                                    <i class="fas fa-check-circle"></i> Present
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block"></i>
                            No registrations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($filtered->count())
            @php
                $totalAttended = $filtered->where('attended', true)->count();
            @endphp
            <tfoot>
                <tr style="background:#0a1f44;">
                    <td colspan="4" class="px-4 py-3 text-white font-bold text-sm">Summary</td>
                    <td class="px-4 py-3 text-white text-sm">
                        <span class="font-bold">{{ $filtered->count() }}</span> Registered
                    </td>
                    <td class="px-4 py-3 no-print"></td>
                    <td class="px-4 py-3 text-white text-sm">
                        <span class="font-bold">{{ $totalAttended }}</span> Attended
                        <span class="text-gray-300 text-xs ml-1">
                            ({{ $filtered->count() > 0 ? round(($totalAttended / $filtered->count()) * 100) : 0 }}%)
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    @if($filtered->count())
        <div class="px-6 py-3 bg-gray-50 border-t text-sm text-gray-500 no-print">
            Sorted by: Category rank → Name (alphabetical)
        </div>
    @endif
</div>
{{-- Attendance Modal --}}
<div id="attendance-modal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,0.55);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b rounded-t-2xl" style="background:#7c3aed;">
                <div class="flex items-center gap-2 text-white">
                    <i class="fas fa-clipboard-check text-xl"></i>
                    <div>
                        <h3 class="font-bold text-lg leading-tight">Mark Attendance</h3>
                        <p class="text-purple-200 text-xs">{{ $event->title }}</p>
                    </div>
                </div>
                <button onclick="closeAttendanceModal()" class="text-white hover:text-purple-200 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Search --}}
            <div class="px-6 pt-5 pb-3">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Search Participant</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input id="attendance-search" type="text"
                        placeholder="Type name, phone or email..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-400 text-sm"
                        autocomplete="off"
                        oninput="searchAttendees(this.value)">
                </div>
                <p id="search-hint" class="text-xs text-gray-400 mt-1">Type at least 2 characters to search.</p>
            </div>

            {{-- Results --}}
            <div id="attendance-results" class="px-6 pb-6 max-h-72 overflow-y-auto space-y-2">
                {{-- populated by JS --}}
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 border-t bg-gray-50 rounded-b-2xl text-xs text-gray-400 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                Click <strong class="text-purple-700 mx-1">Mark Present</strong> to toggle attendance. Changes save instantly.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const SEARCH_URL  = "{{ route('admin.reports.events.attendance.search', $event) }}";
const MARK_URL    = "{{ url('admin/reports/events/' . $event->id . '/attendance') }}";
const CSRF        = "{{ csrf_token() }}";

let searchTimer = null;

function openAttendanceModal() {
    document.getElementById('attendance-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('attendance-search').focus(), 100);
}

function closeAttendanceModal() {
    document.getElementById('attendance-modal').classList.add('hidden');
    document.getElementById('attendance-search').value = '';
    document.getElementById('attendance-results').innerHTML = '';
    document.getElementById('search-hint').textContent = 'Type at least 2 characters to search.';
}

// Close on backdrop click
document.getElementById('attendance-modal').addEventListener('click', function(e) {
    if (e.target === this) closeAttendanceModal();
});

function searchAttendees(q) {
    clearTimeout(searchTimer);
    const resultsEl = document.getElementById('attendance-results');
    const hintEl    = document.getElementById('search-hint');

    if (q.length < 2) {
        resultsEl.innerHTML = '';
        hintEl.textContent = 'Type at least 2 characters to search.';
        return;
    }

    hintEl.textContent = 'Searching...';
    searchTimer = setTimeout(() => {
        fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            hintEl.textContent = data.length ? `${data.length} result(s) found.` : 'No participants found.';
            renderResults(data);
        });
    }, 300);
}

function renderResults(data) {
    const el = document.getElementById('attendance-results');
    if (!data.length) {
        el.innerHTML = `<div class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-user-slash text-3xl mb-2 block"></i>No matching participants.</div>`;
        return;
    }

    el.innerHTML = data.map(r => `
        <div class="flex items-center justify-between p-3 rounded-xl border ${r.attended ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-white'}" id="row-${r.id}">
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">${r.full_name}</p>
                <p class="text-xs text-gray-400">${r.phone}${r.email ? ' · ' + r.email : ''}</p>
                ${r.attended && r.attended_at ? `<p class="text-xs text-purple-600 mt-0.5"><i class="fas fa-clock mr-1"></i>Marked at ${r.attended_at}</p>` : ''}
            </div>
            <button onclick="toggleAttendance(${r.id})"
                id="btn-${r.id}"
                class="ml-3 flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition-all ${r.attended ? 'bg-purple-600 text-white hover:bg-purple-700' : 'border border-purple-400 text-purple-600 hover:bg-purple-50'}">
                ${r.attended ? '<i class="fas fa-check mr-1"></i>Present' : 'Mark Present'}
            </button>
        </div>
    `).join('');
}

function toggleAttendance(id) {
    const btn = document.getElementById(`btn-${id}`);
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch(`${MARK_URL}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        const row = document.getElementById(`row-${id}`);
        const attended = data.attended;

        // Update button
        btn.disabled = false;
        btn.className = `ml-3 flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition-all ${attended ? 'bg-purple-600 text-white hover:bg-purple-700' : 'border border-purple-400 text-purple-600 hover:bg-purple-50'}`;
        btn.innerHTML = attended ? '<i class="fas fa-check mr-1"></i>Present' : 'Mark Present';

        // Update row style
        row.className = `flex items-center justify-between p-3 rounded-xl border ${attended ? 'border-purple-300 bg-purple-50' : 'border-gray-200 bg-white'}`;

        // Update time if present
        const nameEl = row.querySelector('p.font-semibold');
        let timeEl = row.querySelector('p.text-purple-600');
        if (attended) {
            if (!timeEl) {
                timeEl = document.createElement('p');
                timeEl.className = 'text-xs text-purple-600 mt-0.5';
                nameEl.parentNode.appendChild(timeEl);
            }
            timeEl.innerHTML = `<i class="fas fa-clock mr-1"></i>Marked at ${data.attended_at}`;
        } else if (timeEl) {
            timeEl.remove();
        }

        // Also update the main table row
        const cell = document.getElementById(`attended-cell-${id}`);
        if (cell) {
            cell.innerHTML = attended
                ? `<span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-purple-100 text-purple-700"><i class="fas fa-check-circle"></i> Present</span>`
                : `<span class="text-xs text-gray-400">—</span>`;
        }
    });
}
</script>
@endpush

@endsection
