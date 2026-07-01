@extends('layouts.app')

@section('title', 'Give / Givings — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-8" style="background: #0a1f44;">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <i class="fas fa-hand-holding-heart text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-4xl font-bold text-white mb-2">Give / Givings</h1>
        <p class="text-gray-300">Support the work of God through Chrisco Upper Room Fellowship</p>
    </div>
</section>

<section class="py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            {{-- M-Pesa Instructions --}}
            <div class="rounded-xl text-white p-8 shadow-xl" style="background: #0a1f44;">
                <h2 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fas fa-mobile-alt mr-3 text-yellow-400"></i>M-Pesa Giving
                </h2>

                <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-6 text-center">
                    <p class="text-gray-300 text-sm mb-1">Paybill Number</p>
                    <p class="text-4xl font-bold text-yellow-400">566422</p>
                    <p class="text-gray-300 text-sm mt-2">Account Number: <span class="text-white font-semibold">Your Name - Type of Giving</span></p>
                    <p class="text-gray-400 text-xs mt-1">e.g Gideon-Tithes</p>
                </div>

                <h3 class="font-semibold text-yellow-400 mb-3 uppercase tracking-wider text-sm">How to Give via M-Pesa</h3>
                <ol class="space-y-3 text-sm">
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">1</span>
                        <span>Go to <strong>M-Pesa</strong> on your phone</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">2</span>
                        <span>Select <strong>Lipa na M-Pesa</strong></span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">3</span>
                        <span>Select <strong>Pay Bill</strong></span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">4</span>
                        <span>Enter Paybill: <strong class="text-yellow-400">566422</strong></span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">5</span>
                        <span>Account: <strong>Your Name - Type of Giving</strong> <span class="text-yellow-300">(e.g Gideon-Tithes)</span></span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">6</span>
                        <span>Enter your <strong>desired amount</strong></span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #c0392b;">7</span>
                        <span>Enter your M-Pesa PIN and <strong>confirm</strong></span>
                    </li>
                </ol>

                <div class="mt-6 p-3 rounded-lg text-center text-sm" style="background: rgba(240,165,0,0.15); border: 1px solid #f0a500;">
                    <i class="fas fa-info-circle text-yellow-400 mr-1"></i>
                    After payment, fill in the transaction code in the form to confirm your gift.
                </div>
            </div>

            {{-- Online Giving Form --}}
            <div class="bg-white rounded-xl shadow-xl p-8">
                <h2 class="text-2xl font-bold mb-6" style="color: #0a1f44;">
                    <i class="fas fa-edit mr-2 text-red-600"></i>Confirm Your Giving
                </h2>

                <form method="POST" action="{{ route('give.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="donor_name" value="{{ old('donor_name') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('full_name') border-red-400 @enderror"
                            placeholder="Gideon Kiplangat" required>
                        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror"
                            placeholder="0726900700" required>
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Amount (KES) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-400 @enderror"
                            placeholder="500" min="1" required>
                        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Giving Type <span class="text-red-500">*</span></label>
                        <select name="giving_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('giving_type') border-red-400 @enderror" required>
                            <option value="">-- Select Giving Type --</option>
                            <option value="tithe" {{ old('giving_type') === 'tithe' ? 'selected' : '' }}>Tithe</option>
                            <option value="offering" {{ old('giving_type') === 'offering' ? 'selected' : '' }}>Offering</option>
                            <option value="thanksgiving" {{ old('giving_type') === 'thanksgiving' ? 'selected' : '' }}>Thanksgiving</option>
                            <option value="building_fund" {{ old('giving_type') === 'building_fund' ? 'selected' : '' }}>Building Fund</option>
                            <option value="missions" {{ old('giving_type') === 'missions' ? 'selected' : '' }}>Missions</option>
                            <option value="other" {{ old('giving_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('giving_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">M-Pesa Transaction Code <span class="text-red-500">*</span></label>
                        <input type="text" name="transaction_code" value="{{ old('transaction_code') }}"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('transaction_code') border-red-400 @enderror"
                            placeholder="QHK1XXXXXXX" required>
                        @error('transaction_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn-red w-full text-center text-lg font-semibold py-3">
                        <i class="fas fa-heart mr-2"></i>Submit Giving
                    </button>
                </form>
            </div>
        </div>

        {{-- Scripture --}}
        <div class="mt-12 text-center py-8 px-4 rounded-xl" style="background: #0a1f44;">
            <p class="text-yellow-400 text-lg italic">"Each of you should give what you have decided in your heart to give,
            not reluctantly or under compulsion, for God loves a cheerful giver."</p>
            <p class="text-gray-300 text-sm mt-2">— 2 Corinthians 9:7</p>
        </div>
    </div>
</section>

@endsection



