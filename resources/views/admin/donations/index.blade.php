@extends('layouts.admin')

@section('title', 'Givings')
@section('page-title', 'Givings')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Givings</h1>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-6">
    <form method="GET" action="{{ route('admin.donations.index') }}" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="Search by name, phone..."
            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1 min-w-48">
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="giving_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Types</option>
            <option value="tithe" {{ request('giving_type') === 'tithe' ? 'selected' : '' }}>Tithe</option>
            <option value="offering" {{ request('giving_type') === 'offering' ? 'selected' : '' }}>Offering</option>
            <option value="thanksgiving" {{ request('giving_type') === 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
            <option value="building_fund" {{ request('giving_type') === 'building_fund' ? 'selected' : '' }}>Building Fund</option>
            <option value="missions" {{ request('giving_type') === 'missions' ? 'selected' : '' }}>Missions</option>
            <option value="other" {{ request('giving_type') === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        <button type="submit" class="btn-red px-4 py-2 text-sm">Filter</button>
        <a href="{{ route('admin.donations.index') }}" class="btn-navy px-4 py-2 text-sm">Reset</a>
    </form>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($donations->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Donor</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Transaction Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($donations as $donation)
                        @php
                            $sc = match($donation->status ?? 'pending') {
                                'confirmed' => 'bg-green-100 text-green-700',
                                'pending'   => 'bg-yellow-100 text-yellow-700',
                                'rejected'  => 'bg-red-100 text-red-700',
                                default     => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $donation->donor_name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $donation->phone }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">KES {{ number_format($donation->amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-500 capitalize">{{ str_replace('_',' ',$donation->giving_type) }}</td>
                            <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $donation->transaction_code }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">
                                    {{ ucfirst($donation->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ \Carbon\Carbon::parse($donation->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.donations.show', $donation) }}"
                                    class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $donations->withQueryString()->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-hand-holding-usd text-5xl mb-3"></i>
                <p>No givings found.</p>
            </div>
        @endif
    </div>
</div>

@endsection


