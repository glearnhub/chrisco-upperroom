@extends('layouts.admin')
@section('title', 'Add Admin User')
@section('page-title', 'Add Admin User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('admin.settings.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Back to System Users
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">New Admin User</h2>

        <form method="POST" action="{{ route('admin.settings.users.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password *</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Roles</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                    @if(!$role->is_super_admin)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                            {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                            class="w-4 h-4 rounded">
                        <span>{{ $role->name }}</span>
                        @if($role->description)
                            <span class="text-gray-400 text-xs">({{ $role->description }})</span>
                        @endif
                    </label>
                    @endif
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="px-6 py-2 rounded-lg text-white text-sm font-semibold" style="background:#0a1f44;">
                    <i class="fas fa-save mr-1"></i> Create User
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 text-sm font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
