@extends('layouts.admin')

@section('title', 'Deacon/Deaconess Report')
@section('page-title', 'Deacon/Deaconess Report')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Deacon / Deaconess Report</h1>
    <div class="flex gap-2">
        @if($selectedDeacon)
        <a href="{{ route('admin.members.report', ['deacon' => $selectedDeacon, 'export' => 'csv']) }}"
           class="px-4 py-2 text-sm text-white rounded" style="background: #16a34a;">
            <i class="fas fa-file-csv mr-2"></i>Export CSV
        </a>
        @endif
        <a href="{{ route('admin.members.index') }}" class="btn-navy px-4 py-2 text-sm">
            <i class="fas fa-arrow-left mr-2"></i>Back to Members
        </a>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.members.report') }}" class="bg-white rounded-xl shadow p-5 mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Deacon / Deaconess</label>
    <div class="flex gap-3">
        <select name="deacon" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 flex-1 max-w-xs">
            <option value="">— Show All (unfiltered) —</option>
            @foreach($deacons as $d)
                <option value="{{ $d }}" {{ $selectedDeacon === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-navy px-5 py-2 text-sm">
            <i class="fas fa-filter mr-1"></i>View Members
        </button>
        @if($selectedDeacon)
            <a href="{{ route('admin.members.report') }}" class="px-4 py-2 text-sm border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Clear</a>
        @endif
    </div>
</form>

@if($selectedDeacon)
<div class="mb-4 p-4 rounded-xl text-white flex items-center space-x-3" style="background: #0a1f44;">
    <i class="fas fa-user-shield text-yellow-400 text-2xl"></i>
    <div>
        <p class="font-bold text-lg">{{ $selectedDeacon }}</p>
        <p class="text-gray-300 text-sm">{{ $members->count() }} member(s) assigned</p>
    </div>
</div>
@endif

@if($members->count())
<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-max">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">#</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Gender</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Phone</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">County</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Home Cell</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Department</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Deacon/Deaconess</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Joined CUR</th>
                <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($members as $i => $member)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <p class="font-semibold text-gray-800">
                        {{ $member->name }}{{ $member->last_name ? ' ' . $member->last_name : '' }}
                    </p>
                    @if($member->middle_name)
                        <p class="text-xs text-gray-400">{{ $member->middle_name }}</p>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600 capitalize">{{ $member->gender ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $member->phone ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $member->email }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $member->county ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $member->home_cell ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs">{{ $member->department ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600 text-xs font-medium" style="color: #0a1f44;">{{ $member->deacon_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs whitespace-nowrap">
                    {{ $member->membership_date ? $member->membership_date->format('M Y') : '—' }}
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.members.show', $member) }}" class="text-blue-500 hover:text-blue-700 text-xs">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Summary by deacon (when no filter) --}}
@else
    @if(!$selectedDeacon && $deacons->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($deacons as $d)
            @php $count = \App\Models\User::where('deacon_name', $d)->count(); @endphp
            <a href="{{ route('admin.members.report', ['deacon' => $d]) }}"
               class="bg-white rounded-xl shadow p-5 hover:shadow-md transition-shadow flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0"
                     style="background: #c0392b;">
                    {{ strtoupper(substr($d, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800">{{ $d }}</p>
                    <p class="text-sm text-gray-500">{{ $count }} member(s)</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 ml-auto"></i>
            </a>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
        <i class="fas fa-users text-4xl mb-3"></i>
        <p>No members found for this deacon/deaconess.</p>
    </div>
    @endif
@endif

@endsection


