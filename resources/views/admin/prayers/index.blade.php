@extends('layouts.admin')

@section('title', 'Prayer Requests')
@section('page-title', 'Prayer Requests')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Prayer Requests</h1>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($prayers->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Request</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Visibility</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($prayers as $prayer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $prayer->is_anonymous ? 'Anonymous' : $prayer->name }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $prayer->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 max-w-xs">
                                {{ Str::limit($prayer->request, 80) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full {{ $prayer->is_public ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $prayer->is_public ? 'Public' : 'Private' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $sc = match($prayer->status ?? 'pending') {
                                        'answered' => 'bg-green-100 text-green-700',
                                        'pending'  => 'bg-yellow-100 text-yellow-700',
                                        'praying'  => 'bg-blue-100 text-blue-700',
                                        default    => 'bg-gray-100 text-gray-500',
                                    };
                                @endphp
                                <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">
                                    {{ ucfirst($prayer->status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400 text-xs">
                                {{ \Carbon\Carbon::parse($prayer->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.prayers.updateStatus', $prayer) }}" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none">
                                        <option value="pending"  {{ ($prayer->status ?? 'pending') === 'pending'  ? 'selected' : '' }}>Pending</option>
                                        <option value="prayed"   {{ ($prayer->status ?? '') === 'prayed'   ? 'selected' : '' }}>Prayed</option>
                                        <option value="answered" {{ ($prayer->status ?? '') === 'answered' ? 'selected' : '' }}>Answered</option>
                                    </select>
                                    <button type="submit" class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $prayers->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-praying-hands text-5xl mb-3"></i>
                <p>No prayer requests yet.</p>
            </div>
        @endif
    </div>
</div>

@endsection


