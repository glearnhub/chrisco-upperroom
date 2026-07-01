@extends('layouts.admin')

@section('title', 'Giving Detail')
@section('page-title', 'Giving Detail')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Giving #{{ $donation->id }}</h1>
    <a href="{{ route('admin.donations.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- Donation Info --}}
    <div class="md:col-span-2 bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-lg mb-4" style="color: #0a1f44;">Giving Information</h2>
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 font-medium">Donor Name</dt>
                <dd class="text-gray-800 font-semibold mt-1">{{ $donation->donor_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Phone</dt>
                <dd class="text-gray-800 mt-1">{{ $donation->phone }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Amount</dt>
                <dd class="text-2xl font-bold mt-1" style="color: #0a1f44;">KES {{ number_format($donation->amount, 2) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Giving Type</dt>
                <dd class="text-gray-800 mt-1 capitalize">{{ str_replace('_', ' ', $donation->giving_type) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Payment Method</dt>
                <dd class="text-gray-800 mt-1">{{ $donation->payment_method ?? 'M-Pesa' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Transaction Code</dt>
                <dd class="text-gray-800 font-mono mt-1">{{ $donation->transaction_code }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Date Submitted</dt>
                <dd class="text-gray-800 mt-1">{{ \Carbon\Carbon::parse($donation->created_at)->format('F d, Y g:i A') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 font-medium">Current Status</dt>
                <dd class="mt-1">
                    @php
                        $sc = match($donation->status ?? 'pending') {
                            'confirmed' => 'bg-green-100 text-green-700',
                            'pending'   => 'bg-yellow-100 text-yellow-700',
                            'rejected'  => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <span class="text-sm px-3 py-1 rounded-full font-semibold {{ $sc }}">
                        {{ ucfirst($donation->status ?? 'pending') }}
                    </span>
                </dd>
            </div>
            @if($donation->notes)
                <div class="col-span-2">
                    <dt class="text-gray-500 font-medium">Notes</dt>
                    <dd class="text-gray-700 mt-1">{{ $donation->notes }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Update Status --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="font-bold text-lg mb-4" style="color: #0a1f44;">Update Status</h2>
        <form method="POST" action="{{ route('admin.donations.updateStatus', $donation) }}">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" {{ ($donation->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ ($donation->status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="rejected" {{ ($donation->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <button type="submit" class="btn-red w-full text-center py-2">
                <i class="fas fa-save mr-2"></i>Update Status
            </button>
        </form>
    </div>

</div>

@endsection


