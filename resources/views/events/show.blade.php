@extends('layouts.app')

@section('title', $event->title . ' — Chrisco Upper Room Fellowship')

@section('content')

@php
    $dynStatus = $event->dynamic_status;
    $statusColor = match($dynStatus) {
        'upcoming'  => 'bg-green-500',
        'ongoing'   => 'bg-blue-500',
        'cancelled' => 'bg-red-600',
        default     => 'bg-gray-500',
    };
    $isSundayService = str_contains(strtolower($event->title), 'sunday service');
@endphp

{{-- Title bar --}}
<div style="background: #0a1f44;" class="py-6">
    <div class="max-w-4xl mx-auto px-4">
        <a href="{{ route('events.index') }}" class="text-yellow-400 hover:text-yellow-300 text-sm mb-3 inline-block">
            <i class="fas fa-arrow-left mr-1"></i>Back to Events
        </a>
        <div class="flex items-start gap-3 flex-wrap">
            <span class="text-xs font-bold px-3 py-1 rounded-full text-white {{ $statusColor }} mt-1 flex-shrink-0">
                {{ ucfirst($dynStatus) }}
            </span>
            <h1 class="text-xl sm:text-2xl md:text-4xl font-bold text-white leading-tight" style="font-family: 'Playfair Display', serif;">
                {{ $event->title }}
            </h1>
        </div>
    </div>
</div>

{{-- Main Content --}}
<section class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Left: About + Description --}}
            <div class="md:col-span-2 space-y-6">

                {{-- Event Image --}}
                @if($event->image)
                    <div class="relative rounded-xl overflow-hidden shadow">
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                            class="w-full object-cover" style="max-height: 280px;">
                        @if($event->status === 'cancelled')
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span style="transform:rotate(-35deg);color:#ff0000;font-size:2.4rem;font-weight:900;letter-spacing:0.12em;font-family:Arial,sans-serif;text-shadow:1px 1px 0 #000,-1px -1px 0 #000,1px -1px 0 #000,-1px 1px 0 #000;border:5px solid #ff0000;padding:6px 18px;white-space:nowrap;background:rgba(0,0,0,0.4);-webkit-text-stroke:1px #cc0000;">CANCELLED</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- About This Event --}}
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold mb-4 pb-2 border-b" style="color: #0a1f44; border-color: #c0392b;">
                        <i class="fas fa-info-circle mr-2" style="color: #c0392b;"></i>About This Event
                    </h2>
                    @if($event->description)
                        <div class="text-gray-700 leading-relaxed text-base" style="white-space: pre-wrap;">{{ $event->description }}</div>
                    @else
                        <p class="text-gray-400 italic">No description provided for this event.</p>
                    @endif
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>{{ session('error') }}
                    </div>
                @endif

            </div>

            {{-- Right: Event Details + Registration --}}
            <div class="space-y-4">

                {{-- Event Details --}}
                <div class="bg-white rounded-xl shadow p-5">
                    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b" style="border-color: #c0392b;">
                        <i class="fas fa-calendar-check mr-2" style="color: #c0392b;"></i>Event Details
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-calendar-alt mt-0.5 flex-shrink-0" style="color: #c0392b;"></i>
                            <span>{{ \Carbon\Carbon::parse($event->start_datetime)->format('F d, Y') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-clock mt-0.5 flex-shrink-0" style="color: #c0392b;"></i>
                            <span>
                                {{ \Carbon\Carbon::parse($event->start_datetime)->format('g:i A') }}
                                @if($event->end_datetime)
                                    – {{ \Carbon\Carbon::parse($event->end_datetime)->format('g:i A') }}
                                @endif
                            </span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt mt-0.5 flex-shrink-0" style="color: #c0392b;"></i>
                            <span>{{ $event->location }}</span>
                        </li>
                        @if($event->capacity)
                            <li class="flex items-start gap-2">
                                <i class="fas fa-users mt-0.5 flex-shrink-0" style="color: #c0392b;"></i>
                                <span>Capacity: {{ $event->capacity }}
                                    <span class="text-gray-400">({{ $registrationCount }} registered)</span>
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>

                {{-- Registration --}}
                @if($dynStatus === 'cancelled')
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 text-center text-red-600 text-sm font-semibold">
                        <i class="fas fa-ban mr-2"></i>This event has been cancelled.
                    </div>
                @elseif($event->registration_required && !$isSundayService)
                    <div class="bg-white rounded-xl shadow p-5">
                        <h3 class="font-bold text-gray-800 mb-3 pb-2 border-b" style="border-color: #c0392b;">
                            <i class="fas fa-user-plus mr-2" style="color: #c0392b;"></i>Registration
                        </h3>
                        <button onclick="openEmailModal()" class="btn-red w-full text-center py-2.5">
                            <i class="fas fa-user-plus mr-2"></i>Register Now
                        </button>
                    </div>
                @endif

            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('events.index') }}" class="btn-navy">
                <i class="fas fa-arrow-left mr-2"></i>Back to All Events
            </a>
        </div>
    </div>
</section>

{{-- STEP 1: Email Lookup Modal --}}
<div id="email-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-3 p-5 sm:p-8">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-bold" style="color:#0a1f44;"><i class="fas fa-envelope mr-2 text-red-600"></i>Enter Your Email</h3>
            <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <p class="text-gray-500 text-sm mb-5">We will check if you are already in our church database and pre-fill your details.</p>
        <input type="email" id="lookup-email" placeholder="your@email.com"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm mb-2">
        <div id="email-error" class="text-red-500 text-xs mb-3 hidden"></div>
        <button onclick="lookupEmail()" class="btn-red w-full py-3 font-semibold mt-2">
            <i class="fas fa-search mr-2"></i>Continue
        </button>
        <p class="text-center text-xs text-gray-400 mt-4">
            No email? <a href="#" onclick="skipEmail(); return false;" class="text-blue-600 underline">Skip and fill manually</a>
        </p>
    </div>
</div>

{{-- STEP 2: Registration Form Modal --}}
<div id="register-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-3 p-5 sm:p-8" style="max-height:90vh; overflow-y:auto;">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-bold" style="color:#0a1f44;"><i class="fas fa-clipboard-list mr-2 text-red-600"></i>Event Registration</h3>
            <button onclick="closeRegisterModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>

        <div id="member-found-banner" class="hidden bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-green-700 text-sm mb-4">
            <i class="fas fa-check-circle mr-1"></i> Details pre-filled from our church records.
        </div>

        <form method="POST" action="{{ route('events.register', $event) }}">
            @csrf
            <input type="hidden" name="member_id" id="field-member-id">
            <input type="hidden" name="category" id="field-category-hidden" value="visitor">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" id="field-full-name" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('full_name') border-red-400 @enderror"
                    placeholder="Gideon Kiplangat" value="{{ old('full_name') }}">
                @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" id="field-phone" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('phone') border-red-400 @enderror"
                    placeholder="+254 726 900 700" value="{{ old('phone') }}">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="field-email"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                    placeholder="gkiplangat01@gmail.com" value="{{ old('email') }}">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Registration Category <span class="text-red-500">*</span></label>
                <select name="category" id="field-category" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-400 @enderror">
                    <option value="">-- Select Category --</option>
                    <option value="presbyter" {{ old('category') === 'presbyter' ? 'selected' : '' }}>Presbyter</option>
                    <option value="pastor"    {{ old('category') === 'pastor'    ? 'selected' : '' }}>Pastor</option>
                    <option value="elder"     {{ old('category') === 'elder'     ? 'selected' : '' }}>Elder</option>
                    <option value="deacon"    {{ old('category') === 'deacon'    ? 'selected' : '' }}>Deacon</option>
                    <option value="deaconess" {{ old('category') === 'deaconess' ? 'selected' : '' }}>Deaconess</option>
                    <option value="member"    {{ old('category') === 'member'    ? 'selected' : '' }}>Member</option>
                    <option value="visitor"   {{ old('category') === 'visitor'   ? 'selected' : '' }}>Visitor</option>
                </select>
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-red w-full py-3 text-lg font-semibold">
                <i class="fas fa-check mr-2"></i>Complete Registration
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEmailModal() {
    document.getElementById('email-modal').classList.remove('hidden');
    setTimeout(() => document.getElementById('lookup-email').focus(), 100);
}
function closeEmailModal() {
    document.getElementById('email-modal').classList.add('hidden');
    document.getElementById('email-error').classList.add('hidden');
}
function closeRegisterModal() {
    document.getElementById('register-modal').classList.add('hidden');
}
function openRegisterModal() {
    closeEmailModal();
    document.getElementById('register-modal').classList.remove('hidden');
}

function skipEmail() {
    document.getElementById('field-email').value      = '';
    document.getElementById('field-member-id').value  = '';
    document.getElementById('field-full-name').value  = '';
    document.getElementById('field-phone').value      = '';
    document.getElementById('field-category').value   = 'visitor';
    document.getElementById('field-category').disabled = true;
    document.getElementById('member-found-banner').classList.add('hidden');
    openRegisterModal();
}

async function lookupEmail() {
    const email = document.getElementById('lookup-email').value.trim();
    const errEl = document.getElementById('email-error');
    errEl.classList.add('hidden');

    if (!email || !email.includes('@')) {
        errEl.textContent = 'Please enter a valid email address.';
        errEl.classList.remove('hidden');
        return;
    }

    try {
        const res  = await fetch('{{ route('events.lookup-email') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ email }),
        });
        const data = await res.json();

        document.getElementById('field-email').value = email;

        if (data.found) {
            document.getElementById('field-full-name').value         = data.full_name;
            document.getElementById('field-phone').value             = data.phone;
            document.getElementById('field-member-id').value         = data.member_id;
            document.getElementById('field-category').value          = data.category;
            document.getElementById('field-category-hidden').value   = data.category;
            document.getElementById('field-category').disabled       = false;
            document.getElementById('member-found-banner').classList.remove('hidden');
        } else {
            document.getElementById('field-full-name').value         = '';
            document.getElementById('field-phone').value             = '';
            document.getElementById('field-member-id').value         = '';
            document.getElementById('field-category').value          = 'visitor';
            document.getElementById('field-category').disabled       = true;
            document.getElementById('member-found-banner').classList.add('hidden');
        }
        openRegisterModal();
    } catch (e) {
        errEl.textContent = 'Something went wrong. Please try again.';
        errEl.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('lookup-email').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); lookupEmail(); }
    });

    document.getElementById('field-category').addEventListener('change', function () {
        document.getElementById('field-category-hidden').value = this.value;
    });

    @if($errors->any())
        document.getElementById('register-modal').classList.remove('hidden');
    @endif
});
</script>
@endpush

@endsection
