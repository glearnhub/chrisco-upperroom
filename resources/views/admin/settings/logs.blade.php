@extends('layouts.admin')
@section('title', 'System Logs')
@section('page-title', 'System Logs')

@section('content')
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-800">Activity Log</h2>
        <span class="text-sm text-gray-400">Last 50 entries per page</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#f8fafc;">
                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                    <th class="px-4 py-3">Date/Time</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Action</th>
                    <th class="px-4 py-3">Module</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5 text-gray-500 whitespace-nowrap text-xs">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-4 py-2.5">
                        @if($log->user)
                            <span class="font-medium text-gray-700">{{ $log->user->name }}</span>
                            <br><span class="text-xs text-gray-400">{{ $log->user->email }}</span>
                        @else
                            <span class="text-gray-400 text-xs">System</span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5">
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-700',
                                'update' => 'bg-blue-100 text-blue-700',
                                'delete' => 'bg-red-100 text-red-700',
                                'login'  => 'bg-purple-100 text-purple-700',
                                'logout' => 'bg-gray-100 text-gray-600',
                            ];
                            $colorClass = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $colorClass }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $log->module }}</td>
                    <td class="px-4 py-2.5 text-gray-700 text-xs max-w-xs truncate">{{ $log->description }}</td>
                    <td class="px-4 py-2.5 text-gray-400 text-xs font-mono">{{ $log->ip_address ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No system logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $logs->links() }}</div>
@endsection
