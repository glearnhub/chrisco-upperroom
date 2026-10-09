@extends('layouts.admin')
@section('title', 'System Permissions')
@section('page-title', 'System Permissions & Roles')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ── Roles Panel ── --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold text-gray-800">Roles</h2>
        </div>

        {{-- Add Role Form --}}
        <div class="bg-white rounded-xl shadow p-4 mb-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Add New Role</h3>
            <form method="POST" action="{{ route('admin.settings.roles.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Role Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Content Editor"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Slug * <span class="text-gray-400">(lowercase, dashes)</span></label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="e.g. content-editor"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                        @error('slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Optional short description"
                        class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-1.5 rounded text-white text-sm font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-plus mr-1"></i> Add Role
                </button>
            </form>
        </div>

        {{-- Roles List --}}
        <div class="space-y-3">
            @foreach($roles as $role)
            <div class="bg-white rounded-xl shadow p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="font-semibold text-gray-800 text-sm">{{ $role->name }}</span>
                        @if($role->is_super_admin)
                            <span class="ml-2 text-xs px-2 py-0.5 rounded-full text-white" style="background:#f0a500;">SUPER ADMIN</span>
                        @endif
                        <p class="text-xs text-gray-400 mt-0.5">{{ $role->slug }}</p>
                        @if($role->description)<p class="text-xs text-gray-500">{{ $role->description }}</p>@endif
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $role->permissions_count }} permissions · {{ $role->users_count }} users
                        </p>
                    </div>
                    @if(!$role->is_super_admin)
                    <form method="POST" action="{{ route('admin.settings.roles.destroy', $role) }}"
                          data-confirm="Delete role {{ $role->name }}?" data-confirm-ok="Delete">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>

                @if(!$role->is_super_admin)
                {{-- Edit role / sync permissions --}}
                <details class="mt-2">
                    <summary class="text-xs text-blue-600 cursor-pointer hover:underline">Edit permissions</summary>
                    <form method="POST" action="{{ route('admin.settings.roles.update', $role) }}" class="mt-3">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Role Name</label>
                            <input type="text" name="name" value="{{ $role->name }}" required
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                        </div>
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                            <input type="text" name="description" value="{{ $role->description }}"
                                class="w-full border border-gray-300 rounded px-2 py-1 text-xs">
                        </div>
                        <p class="text-xs font-semibold text-gray-600 mb-2">Permissions</p>
                        @foreach($permissions as $module => $perms)
                        <div class="mb-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">{{ $module }}</p>
                            <div class="grid grid-cols-2 gap-1">
                                @foreach($perms as $perm)
                                <label class="flex items-center gap-1 text-xs cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                        {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                    {{ $perm->name }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                        <button type="submit" class="mt-2 px-3 py-1 rounded text-white text-xs font-semibold" style="background:#0a1f44;">
                            Save Role
                        </button>
                    </form>
                </details>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Permissions Panel ── --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold text-gray-800">Permissions</h2>
        </div>

        {{-- Add Permission Form --}}
        <div class="bg-white rounded-xl shadow p-4 mb-4">
            <h3 class="text-sm font-bold text-gray-700 mb-3">Add New Permission</h3>
            <form method="POST" action="{{ route('admin.settings.permissions.store') }}">
                @csrf
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. View Sermons"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Slug *</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="e.g. sermons.view"
                            class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Module *</label>
                    <input type="text" name="module" value="{{ old('module') }}" required placeholder="e.g. Sermons"
                        class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description') }}"
                        class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-1.5 rounded text-white text-sm font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-plus mr-1"></i> Add Permission
                </button>
            </form>
        </div>

        {{-- Permissions grouped by module --}}
        @foreach($permissions as $module => $perms)
        <div class="bg-white rounded-xl shadow p-4 mb-3">
            <h3 class="text-sm font-bold text-gray-700 mb-2 border-b pb-1">{{ $module }}</h3>
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400">
                        <th class="text-left py-1">Name</th>
                        <th class="text-left py-1">Slug</th>
                        <th class="text-right py-1">Delete</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($perms as $perm)
                    <tr>
                        <td class="py-1 text-gray-700">{{ $perm->name }}</td>
                        <td class="py-1 text-gray-500 font-mono">{{ $perm->slug }}</td>
                        <td class="py-1 text-right">
                            <form method="POST" action="{{ route('admin.settings.permissions.destroy', $perm) }}"
                                  data-confirm="Delete permission {{ $perm->slug }}?" data-confirm-ok="Delete">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

</div>
@endsection
