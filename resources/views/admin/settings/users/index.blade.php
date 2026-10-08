@extends('layouts.admin')
@section('title', 'System Users')
@section('page-title', 'System Users')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">System Users</h2>
    <a href="{{ route('admin.settings.users.create') }}"
       class="px-4 py-2 rounded-lg text-white text-sm font-semibold flex items-center gap-2" style="background:#0a1f44;">
        <i class="fas fa-plus"></i> Add Admin User
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead style="background:#0a1f44; color:#fff;">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Roles</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="{{ !$user->is_active ? 'opacity-50' : '' }}">
                <td class="px-4 py-3 font-medium text-gray-800">
                    {{ $user->name }}
                    @if($user->isSuperAdmin())
                        <span class="ml-1 text-xs px-2 py-0.5 rounded-full text-white font-bold" style="background:#f0a500;">SUPER ADMIN</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1">
                        @foreach($user->roles as $role)
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background:#0a1f44;">{{ $role->name }}</span>
                        @endforeach
                        @if($user->roles->isEmpty() && !$user->isSuperAdmin())
                            <span class="text-xs text-gray-400">No roles</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3 text-center">
                    @if($user->is_active)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Active</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('admin.settings.users.edit', $user) }}"
                           class="text-xs px-3 py-1 rounded text-white" style="background:#0a1f44;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @if(!$user->isSuperAdmin())
                        <form method="POST" action="{{ route('admin.settings.users.toggle', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-3 py-1 rounded text-white"
                                style="background:{{ $user->is_active ? '#c0392b' : '#16a34a' }};">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No admin users found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>{{-- /overflow-x-auto --}}
</div>

<div class="mt-4">{{ $users->links() }}</div>
@endsection
