@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Dashboard Overview</h1>
    <p class="text-gray-500 text-sm">Welcome back, {{ auth()->user()->name }}</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-users text-2xl mb-2" style="color: #0a1f44;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $totalMembers ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Members</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-bible text-2xl mb-2" style="color: #c0392b;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $totalSermons ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Sermons</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-hand-holding-usd text-2xl mb-2" style="color: #f0a500;"></i>
        <p class="text-lg font-bold" style="color: #0a1f44;">KES {{ number_format($totalGivings ?? 0) }}</p>
        <p class="text-xs text-gray-500 mt-1">Givings</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-calendar-alt text-2xl mb-2" style="color: #0a1f44;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $upcomingEvents ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Events</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-praying-hands text-2xl mb-2" style="color: #c0392b;"></i>
        <p class="text-2xl font-bold" style="color: #0a1f44;">{{ $pendingPrayers ?? 0 }}</p>
        <p class="text-xs text-gray-500 mt-1">Pending Prayers</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4 text-center">
        <i class="fas fa-broadcast-tower text-2xl mb-2" style="{{ ($activeLivestream ?? false) ? 'color: #c0392b;' : 'color: #9ca3af;' }}"></i>
        <p class="text-lg font-bold" style="color: #0a1f44;">{{ ($activeLivestream ?? false) ? 'LIVE' : 'OFF' }}</p>
        <p class="text-xs text-gray-500 mt-1">Livestream</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent Givings --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Recent Givings</h2>
            <a href="{{ route('admin.donations.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            @if(isset($recentGivings) && $recentGivings->count())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Donor</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Amount</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Type</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentGivings as $d)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-700">{{ $d->donor_name }}</td>
                                <td class="px-4 py-2 font-semibold">KES {{ number_format($d->amount) }}</td>
                                <td class="px-4 py-2 text-gray-500 capitalize">{{ str_replace('_',' ',$d->giving_type) }}</td>
                                <td class="px-4 py-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $d->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($d->status ?? 'pending') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-400 text-sm text-center py-6">No givings yet.</p>
            @endif
        </div>
    </div>

    {{-- Recent Members --}}
    <div class="bg-white rounded-xl shadow">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="font-bold text-gray-800">Recent Members</h2>
            <a href="{{ route('admin.members.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            @if(isset($recentMembers) && $recentMembers->count())
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Name</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Email</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Role</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-500">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentMembers as $m)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-700 font-medium">{{ $m->name }}</td>
                                <td class="px-4 py-2 text-gray-500 text-xs">{{ $m->email }}</td>
                                <td class="px-4 py-2">
                                    <span class="text-xs px-2 py-0.5 rounded-full {{ $m->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $m->role === 'admin' ? 'IT Support' : 'Member' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-400 text-xs">
                                    {{ \Carbon\Carbon::parse($m->created_at)->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-400 text-sm text-center py-6">No members yet.</p>
            @endif
        </div>
    </div>

</div>

{{-- Recent Prayers --}}
<div class="mt-6 bg-white rounded-xl shadow">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="font-bold text-gray-800">Recent Prayer Requests</h2>
        <a href="{{ route('admin.prayers.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
        @if(isset($recentPrayers) && $recentPrayers->count())
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Name</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Request</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Status</th>
                        <th class="px-4 py-2 text-left text-xs text-gray-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentPrayers as $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-700 font-medium">{{ $p->name ?? 'Anonymous' }}</td>
                            <td class="px-4 py-2 text-gray-500 text-xs max-w-xs truncate">{{ Str::limit($p->request, 80) }}</td>
                            <td class="px-4 py-2">
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $p->status === 'answered' ? 'bg-green-100 text-green-700' :
                                       ($p->status === 'prayed' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($p->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-gray-400 text-xs">{{ $p->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-400 text-sm text-center py-6">No prayer requests yet.</p>
        @endif
    </div>
</div>

@endsection


