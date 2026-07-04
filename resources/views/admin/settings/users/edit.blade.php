@extends('layouts.admin')
@section('title', 'Edit Admin User')
@section('page-title', 'Edit Admin User')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.settings.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Back to System Users
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Basic Info & Roles --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">User Info & Roles</h2>

            <form method="POST" action="{{ route('admin.settings.users.update', $user) }}">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                @if(!$user->isSuperAdmin())
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Roles</label>
                    <div class="space-y-1">
                        @foreach($roles as $role)
                        @if(!$role->is_super_admin)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}
                                class="w-4 h-4 rounded">
                            <span>{{ $role->name }}</span>
                        </label>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <button type="submit" class="w-full py-2 rounded-lg text-white text-sm font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
            </form>
        </div>

        <div class="space-y-5">
            {{-- Individual Permission Overrides --}}
            @if(!$user->isSuperAdmin())
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-1">Permission Overrides</h2>
                <p class="text-xs text-gray-500 mb-4">Grant or revoke individual permissions regardless of role.</p>

                <form method="POST" action="{{ route('admin.settings.users.update', $user) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    @foreach($permissions as $module => $perms)
                    <div class="mb-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">{{ $module }}</p>
                        <div class="space-y-1">
                            @foreach($perms as $perm)
                            @php
                                $override = $userPerms->get($perm->id);
                                $currentType = $override ? $override->type : 'none';
                            @endphp
                            <div class="flex items-center justify-between text-xs py-0.5">
                                <span class="text-gray-700">{{ $perm->name }}</span>
                                <div class="flex gap-1">
                                    <label class="flex items-center gap-1 cursor-pointer">
                                        <input type="radio" name="perm_overrides[{{ $perm->id }}]" value="grant"
                                            {{ $currentType === 'grant' ? 'checked' : '' }}>
                                        <span class="text-green-600">Grant</span>
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer ml-2">
                                        <input type="radio" name="perm_overrides[{{ $perm->id }}]" value="revoke"
                                            {{ $currentType === 'revoke' ? 'checked' : '' }}>
                                        <span class="text-red-600">Revoke</span>
                                    </label>
                                    <label class="flex items-center gap-1 cursor-pointer ml-2">
                                        <input type="radio" name="perm_overrides[{{ $perm->id }}]" value="none"
                                            {{ $currentType === 'none' ? 'checked' : '' }}>
                                        <span class="text-gray-400">None</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <button type="submit" class="w-full mt-3 py-2 rounded-lg text-white text-sm font-semibold" style="background:#c0392b;">
                        <i class="fas fa-shield-alt mr-1"></i> Save Overrides
                    </button>
                </form>
            </div>
            @endif

            {{-- Reset Password --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Reset Password</h2>
                <form method="POST" action="{{ route('admin.settings.users.password', $user) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">New Password *</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit" class="w-full py-2 rounded-lg text-white text-sm font-semibold" style="background:#f0a500;">
                        <i class="fas fa-key mr-1"></i> Reset Password
                    </button>
                </form>
            </div>

            {{-- Delete User --}}
            @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
            <div class="bg-white rounded-xl shadow p-6 border border-red-200">
                <h2 class="text-lg font-bold text-red-700 mb-2">Danger Zone</h2>
                <p class="text-xs text-gray-500 mb-3">This action cannot be undone.</p>
                <form method="POST" action="{{ route('admin.settings.users.destroy', $user) }}"
                      onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 rounded-lg text-white text-sm font-semibold bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-1"></i> Delete User
                    </button>
                </form>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
