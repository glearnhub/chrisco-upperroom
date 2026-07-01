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
    <div style="display:flex; justify-content:space-between; font-size:0.78rem; color:#444; padding:8px 0; border-bottom:1px solid #ddd; margin-bottom:6px;">
        <div>
            <strong>Report:</strong> Event Registration Report<br>
            <strong>Event:</strong> {{ $event->title }}<br>
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->start_datetime)->format('F d, Y g:i A') }}<br>
            <strong>Venue:</strong> {{ $event->location }}
        </div>
        <div style="text-align:right;">
            <strong>Generated:</strong> {{ now()->format('d M Y, g:i A') }}<br>
            <strong>By:</strong> {{ auth()->user()->name ?? 'Administrator' }}<br>
            <strong>Total Participants:</strong> {{ $stats['total'] }}
        </div>
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
        <a href="{{ route('admin.reports.events.show', ['event' => $event->id, 'export' => 'excel', 'filter' => $filter, 'search' => $search]) }}"
           class="flex items-center gap-2 px-4 py-2 text-sm rounded-lg text-white font-semibold" style="background:#1a7a3c;">
            <i class="fas fa-file-excel"></i> Excel
        </a>
    </div>
</div>

{{-- Stats Cards --}}
<div class="no-print grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow p-5 text-center border-t-4" style="border-color:#0a1f44;">
        <p class="text-3xl font-bold" style="color:#0a1f44;">{{ $stats['total'] }}</p>
        <p class="text-gray-500 text-sm mt-1">Total Participants</p>
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
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Full Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Phone</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase no-print">Status</th>
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
        </table>
    </div>
    @if($filtered->count())
        <div class="px-6 py-3 bg-gray-50 border-t text-sm text-gray-500 no-print">
            Sorted by: Category rank → Name (alphabetical)
        </div>
    @endif
</div>
@endsection
