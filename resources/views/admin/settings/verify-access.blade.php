@extends('layouts.admin')
@section('title', 'Verify Access Window')
@section('page-title', 'Verify Access Window')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-1 flex items-center gap-2">
            <i class="fas fa-calendar-check" style="color:#f0a500;"></i> Member Verification Access Window
        </h2>
        <p class="text-sm text-gray-500 mb-6">
            Control when the public <code class="bg-gray-100 px-1 rounded">/verify</code> page is accessible.
            Outside the set window, visitors will see an "Access Denied" page.
        </p>

        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg text-sm font-semibold text-white" style="background:#065f46;">
                <i class="fas fa-check mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.verify-access.update') }}">
            @csrf @method('POST')

            {{-- Enable toggle --}}
            <div class="mb-6 p-4 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-between">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Enable access restriction</p>
                    <p class="text-xs text-gray-500 mt-0.5">When off, the verify page is always open to everyone.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="verify_access_enabled" value="1" class="sr-only peer"
                        {{ \App\Models\Setting::get('verify_access_enabled', '0') === '1' ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full peer
                        peer-focus:ring-2 peer-focus:ring-blue-300 transition-colors duration-200
                        after:content-[''] after:absolute after:top-0.5 after:left-0.5
                        after:bg-white after:rounded-full after:h-5 after:w-5
                        after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
            </div>

            {{-- Date range --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <i class="fas fa-calendar-plus mr-1 text-green-600"></i> Access Opens
                    </label>
                    <input type="date" name="verify_access_start"
                        value="{{ old('verify_access_start', \App\Models\Setting::get('verify_access_start')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    @error('verify_access_start')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Leave blank for no start restriction.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <i class="fas fa-calendar-minus mr-1 text-red-500"></i> Access Closes
                    </label>
                    <input type="date" name="verify_access_end"
                        value="{{ old('verify_access_end', \App\Models\Setting::get('verify_access_end')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                    @error('verify_access_end')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 mt-1">Leave blank for no end restriction.</p>
                </div>
            </div>

            {{-- Current status --}}
            @php
                $enabled = \App\Models\Setting::get('verify_access_enabled', '0') === '1';
                $start   = \App\Models\Setting::get('verify_access_start');
                $end     = \App\Models\Setting::get('verify_access_end');
                $now     = now();
                $open    = !$enabled || (
                    (!$start || $now->gte(\Carbon\Carbon::parse($start))) &&
                    (!$end   || $now->lte(\Carbon\Carbon::parse($end)->endOfDay()))
                );
            @endphp
            <div class="mb-6 p-3 rounded-lg text-sm flex items-center gap-2 {{ $open ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                <i class="fas {{ $open ? 'fa-unlock-alt' : 'fa-lock' }}"></i>
                <span>
                    Current status: <strong>{{ $open ? 'Open — visitors can access /verify' : 'Closed — visitors see Access Denied' }}</strong>
                </span>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-lg text-white font-semibold text-sm"
                style="background:#0a1f44;">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </form>
    </div>
</div>
@endsection
