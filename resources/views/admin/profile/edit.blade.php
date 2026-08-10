@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- ── Profile Info ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background:#0a1f44;">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
                <i class="fas fa-user-circle text-yellow-400"></i> Account Information
            </h2>
        </div>
        <form method="POST" action="{{ route('admin.my-profile.update') }}" class="px-6 py-6 space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Display Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Login Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold text-white" style="background:#0a1f44;">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- ── Change Password ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100" style="background:#0a1f44;">
            <h2 class="text-white font-bold text-base flex items-center gap-2">
                <i class="fas fa-lock text-yellow-400"></i> Change Password
            </h2>
        </div>
        <form method="POST" action="{{ route('admin.my-profile.password') }}" class="px-6 py-6 space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Current Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="current_password" id="cur_pw"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-300 @error('current_password') border-red-400 @enderror">
                    <button type="button" onclick="togglePw('cur_pw','eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye1" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                @error('current_password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">New Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password" id="new_pw"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-300 @error('password') border-red-400 @enderror">
                    <button type="button" onclick="togglePw('new_pw','eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye2" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-400 mt-1">Minimum 8 characters.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm New Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="conf_pw"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <button type="button" onclick="togglePw('conf_pw','eye3')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i id="eye3" class="fas fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2 rounded-lg text-sm font-bold text-white" style="background:#c0392b;">
                    <i class="fas fa-key mr-2"></i>Change Password
                </button>
            </div>
        </form>
    </div>

</div>

<script>
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

@endsection
