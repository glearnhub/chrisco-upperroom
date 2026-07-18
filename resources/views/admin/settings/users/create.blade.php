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
        <h2 class="text-xl font-bold text-gray-800 mb-1">New Admin User</h2>
        <p class="text-sm text-gray-500 mb-6">Select an existing church member to grant admin access.</p>

        <form method="POST" action="{{ route('admin.settings.users.store') }}">
            @csrf

            {{-- Member picker --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Select Member *</label>
                <select name="member_id" id="member_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500 @error('member_id') border-red-400 @enderror">
                    <option value="">— Search member by name —</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}"
                            data-email="{{ $member->email }}"
                            {{ old('member_id') == $member->id ? 'selected' : '' }}>
                            {{ $member->name }} {{ $member->last_name }} — {{ $member->email }}
                        </option>
                    @endforeach
                </select>
                @error('member_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Read-only email preview --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email (from member record)</label>
                <input type="text" id="email_preview" disabled placeholder="Populated when member is selected"
                    class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500">
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-semibold text-gray-700">Set Password *</label>
                    <button type="button" onclick="generatePassword()"
                        class="text-xs px-3 py-1 rounded-full font-semibold text-white flex items-center gap-1"
                        style="background:#f0a500; color:#0a1f44;">
                        <i class="fas fa-magic"></i> Suggest Password
                    </button>
                </div>
                <div class="relative">
                    <input type="text" name="password" id="password_field" required minlength="8"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm font-mono focus:outline-none focus:border-blue-500">
                    <button type="button" onclick="copyPassword()" title="Copy password"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                        <i class="fas fa-copy text-sm" id="copy-icon"></i>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <p id="strength-bar" class="text-xs mt-1 text-gray-400"></p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password *</label>
                <input type="text" name="password_confirmation" id="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:border-blue-500">
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
                    <i class="fas fa-user-shield mr-1"></i> Grant Admin Access
                </button>
                <a href="{{ route('admin.settings.users.index') }}" class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 text-sm font-semibold">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const select = document.getElementById('member_id');
    const preview = document.getElementById('email_preview');

    function updatePreview() {
        const opt = select.options[select.selectedIndex];
        preview.value = opt?.dataset?.email ?? '';
    }

    select.addEventListener('change', updatePreview);
    updatePreview();

    function generatePassword() {
        const upper  = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lower  = 'abcdefghjkmnpqrstuvwxyz';
        const digits = '23456789';
        const special = '@#$!%*?&';
        const all = upper + lower + digits + special;

        // Guarantee at least one of each character class
        let pwd = [
            upper[Math.floor(Math.random() * upper.length)],
            upper[Math.floor(Math.random() * upper.length)],
            lower[Math.floor(Math.random() * lower.length)],
            lower[Math.floor(Math.random() * lower.length)],
            digits[Math.floor(Math.random() * digits.length)],
            digits[Math.floor(Math.random() * digits.length)],
            special[Math.floor(Math.random() * special.length)],
            special[Math.floor(Math.random() * special.length)],
        ];

        // Fill to 14 characters
        for (let i = pwd.length; i < 14; i++) {
            pwd.push(all[Math.floor(Math.random() * all.length)]);
        }

        // Shuffle
        pwd = pwd.sort(() => Math.random() - 0.5).join('');

        document.getElementById('password_field').value = pwd;
        document.getElementById('password_confirmation').value = pwd;
        document.getElementById('strength-bar').textContent = '✔ Strong password generated — copy it before saving.';
        document.getElementById('strength-bar').style.color = '#065f46';
    }

    function copyPassword() {
        const val = document.getElementById('password_field').value;
        if (!val) return;
        navigator.clipboard.writeText(val).then(() => {
            const icon = document.getElementById('copy-icon');
            icon.className = 'fas fa-check text-sm';
            icon.style.color = '#065f46';
            setTimeout(() => {
                icon.className = 'fas fa-copy text-sm';
                icon.style.color = '';
            }, 2000);
        });
    }
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect('#member_id', {
            placeholder: 'Search member by name...',
            allowEmptyOption: true,
            onChange: function(value) {
                const opt = document.querySelector('#member_id option[value="' + value + '"]');
                document.getElementById('email_preview').value = opt?.dataset?.email ?? '';
            }
        });
    });
</script>
@endsection
