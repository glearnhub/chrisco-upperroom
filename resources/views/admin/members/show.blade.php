@extends('layouts.admin')

@section('title', 'Member Profile')
@section('page-title', 'Member Profile')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">{{ $member->name }}</h1>
    <a href="{{ route('admin.members.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Profile Card --}}
    <div class="bg-white rounded-xl shadow p-6">
        <div class="text-center mb-4">
            <div class="w-20 h-20 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold mb-3" style="background: #0a1f44;">
                {{ strtoupper(substr($member->name, 0, 1)) }}
            </div>
            <h2 class="font-bold text-lg text-gray-800">{{ $member->name }}</h2>
            <p class="text-gray-500 text-sm">{{ $member->email }}</p>
            <span class="mt-2 inline-block text-xs px-3 py-1 rounded-full {{ $member->role === 'admin' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                {{ $member->role === 'admin' ? 'IT Support' : 'Member' }}
            </span>
        </div>
        <dl class="space-y-2 text-sm border-t pt-4">
            <div class="flex justify-between">
                <dt class="text-gray-500">Phone</dt>
                <dd class="text-gray-800">{{ $member->phone ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Membership Date</dt>
                <dd class="text-gray-800">
                    {{ $member->membership_date ? \Carbon\Carbon::parse($member->membership_date)->format('M d, Y') : '—' }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500">Joined</dt>
                <dd class="text-gray-800">{{ \Carbon\Carbon::parse($member->created_at)->format('M d, Y') }}</dd>
            </div>
        </dl>

        {{-- Role Change Form --}}
        <div class="mt-5 pt-4 border-t">
            <h3 class="font-semibold text-sm text-gray-700 mb-3">Change Role</h3>
            <form method="POST" action="{{ route('admin.members.updateRole', $member) }}">
                @csrf
                @method('PATCH')
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3 focus:outline-none">
                    <option value="member" {{ ($member->role ?? 'member') === 'member' ? 'selected' : '' }}>Member</option>
                    <option value="admin" {{ ($member->role ?? '') === 'admin' ? 'selected' : '' }}>IT Support</option>
                </select>
                <button type="submit" class="btn-red w-full text-center text-sm py-2">Update Role</button>
            </form>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Giving History --}}
        <div class="bg-white rounded-xl shadow">
            <div class="px-5 py-4 border-b">
                <h2 class="font-bold text-gray-800">Giving History</h2>
            </div>
            @if(isset($donations) && $donations->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Date</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Amount</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($donations as $d)
                                <tr>
                                    <td class="px-4 py-2 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($d->created_at)->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-gray-600 capitalize">{{ str_replace('_',' ',$d->giving_type) }}</td>
                                    <td class="px-4 py-2 font-bold text-green-700">KES {{ number_format($d->amount) }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs px-2 py-0.5 rounded-full {{ $d->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            {{ ucfirst($d->status ?? 'pending') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">No givings recorded.</p>
            @endif
        </div>

        {{-- Event Registrations --}}
        <div class="bg-white rounded-xl shadow">
            <div class="px-5 py-4 border-b">
                <h2 class="font-bold text-gray-800">Event Registrations</h2>
            </div>
            @if(isset($eventRegistrations) && $eventRegistrations->count())
                <div class="p-4 space-y-2">
                    @foreach($eventRegistrations as $reg)
                        <div class="flex justify-between items-center text-sm py-2 border-b last:border-0">
                            <span class="font-medium text-gray-700">{{ $reg->event->title ?? '—' }}</span>
                            <span class="text-gray-400 text-xs">
                                {{ $reg->event?->start_datetime ? \Carbon\Carbon::parse($reg->event->start_datetime)->format('M d, Y') : '' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center py-6">No event registrations.</p>
            @endif
        </div>

    </div>
</div>

@endsection


