@extends('layouts.admin')

@section('title', 'Members')
@section('page-title', 'Members')

@push('styles')
<style>
@media print {
    .sidebar, header, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    table { font-size: 11px; }
}
</style>
@endpush

@section('content')

<div class="flex flex-wrap items-center justify-between gap-3 mb-6 no-print">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Members</h1>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.members.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#c0392b;">
            <i class="fas fa-plus"></i> Add Member
        </a>
        <a href="{{ route('admin.members.import') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#7c3aed;">
            <i class="fas fa-file-excel"></i> Import Excel
        </a>
        <a href="{{ route('admin.members.index', array_merge(request()->query(), ['export'=>'csv'])) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#16a34a;">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
        <a href="{{ route('admin.members.print', request()->query()) }}" target="_blank"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.members.index') }}" class="bg-white rounded-xl shadow p-4 mb-5">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name / email / phone..."
               class="col-span-2 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">

        <select name="role" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Roles</option>
            <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>IT Support</option>
        </select>

        <select name="gender" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Genders</option>
            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
        </select>

        <select name="department" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>

        <select name="deacon" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Deacons</option>
            @foreach($deacons as $d)
                <option value="{{ $d }}" {{ request('deacon') === $d ? 'selected' : '' }}>{{ $d }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2 mt-3">
        <button type="submit" class="btn-navy px-4 py-1.5 text-sm">
            <i class="fas fa-search mr-1"></i>Filter
        </button>
        <a href="{{ route('admin.members.index') }}" class="px-4 py-1.5 text-sm border border-gray-300 rounded text-gray-600 hover:bg-gray-50">Reset</a>
    </div>
</form>

<p class="text-sm text-gray-500 mb-3">
    Showing {{ $members->firstItem() }}–{{ $members->lastItem() }} of {{ $members->total() }} members
</p>

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="w-full text-sm min-w-max">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">#</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Name</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Gender</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Phone</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Email</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">County</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Department</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Home Cell</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Deacon/Deaconess</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Joined CUR</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Role</th>
                <th class="px-3 py-3 text-left text-xs text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($members as $i => $member)
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-3 text-gray-400 text-xs">{{ $members->firstItem() + $i }}</td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                             style="background: #0a1f44;">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $member->name }}{{ $member->last_name ? ' ' . $member->last_name : '' }}
                            </p>
                            @if($member->middle_name)
                                <p class="text-xs text-gray-400">{{ $member->middle_name }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-3 py-3 text-gray-600 capitalize">{{ $member->gender ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $member->phone ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-500 text-xs">{{ $member->email }}</td>
                <td class="px-3 py-3 text-gray-600 text-xs whitespace-nowrap">{{ $member->county ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-600 text-xs whitespace-nowrap">{{ $member->department ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-600 text-xs whitespace-nowrap">{{ $member->home_cell ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-600 text-xs whitespace-nowrap">{{ $member->deacon_name ?? '—' }}</td>
                <td class="px-3 py-3 text-gray-400 text-xs whitespace-nowrap">
                    {{ $member->membership_date ? $member->membership_date->format('M Y') : '—' }}
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $member->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $member->role === 'admin' ? 'IT Support' : 'Member' }}
                    </span>
                </td>
                <td class="px-3 py-3 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.members.show', $member) }}" class="text-blue-500 hover:text-blue-700" title="View">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('admin.members.edit', $member) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="px-4 py-10 text-center text-gray-400">No members found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3">{{ $members->withQueryString()->links() }}</div>
</div>

@endsection


