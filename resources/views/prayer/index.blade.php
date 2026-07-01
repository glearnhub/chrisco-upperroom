@extends('layouts.app')

@section('title', 'Prayer Requests — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <i class="fas fa-praying-hands text-5xl mb-3 text-yellow-400"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Prayer Requests</h1>
        <p class="text-gray-300">"The effective prayer of a righteous person can accomplish much." — James 5:16</p>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- Submit Prayer Request Form --}}
            <div>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6" style="color: #0a1f44;">
                        <i class="fas fa-paper-plane mr-2 text-red-600"></i>Submit a Prayer Request
                    </h2>

                    <style>
                        ::placeholder { font-size: 0.78rem; }
                    </style>
                    <form method="POST" action="{{ route('prayer.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                                placeholder="Gideon Kiplangat" required>
                            <p class="text-gray-400 text-xs mt-1">You may tick "Submit Anonymously" below to hide your name publicly.</p>
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror"
                                placeholder="+254 726 900 700" required>
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                                placeholder="gkiplangat01@gmail.com">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Prayer Request <span class="text-red-500">*</span></label>
                            <textarea name="request" rows="5" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('request') border-red-400 @enderror"
                                placeholder="Share your prayer request here...">{{ old('request') }}</textarea>
                            @error('request')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="is_anonymous" value="1" {{ old('is_anonymous') ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600">
                                <span class="text-sm text-gray-700">Submit Anonymously</span>
                            </label>
                        </div>

                        <button type="submit" class="btn-red w-full text-center text-lg font-semibold py-3">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Prayer Request
                        </button>
                    </form>
                </div>
            </div>

            {{-- Public Prayer Wall --}}
            <div>
                <h2 class="text-2xl font-bold mb-6" style="color: #0a1f44;">
                    <i class="fas fa-hands-praying mr-2 text-yellow-600"></i>Prayer Wall
                </h2>

                @if(isset($publicRequests) && $publicRequests->count())
                    <div class="space-y-4 max-h-screen overflow-y-auto pr-1">
                        @foreach($publicRequests as $prayer)
                            <div class="bg-white rounded-xl shadow p-5 border-l-4" style="border-color: #0a1f44;">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-800 text-sm">
                                        <i class="fas fa-user-circle mr-1 text-gray-400"></i>
                                        {{ $prayer->is_anonymous ? 'Anonymous' : $prayer->name }}
                                    </span>
                                    <span class="text-gray-400 text-xs">
                                        {{ \Carbon\Carbon::parse($prayer->created_at)->format('M d, Y') }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm leading-relaxed">{{ Str::limit($prayer->request, 200) }}</p>
                                <div class="mt-3 text-xs text-gray-400">
                                    <i class="fas fa-praying-hands mr-1 text-yellow-500"></i>
                                    Praying with you
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                        <i class="fas fa-praying-hands text-5xl text-gray-300 mb-3"></i>
                        <p>No public prayer requests yet. Be the first to share!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection



