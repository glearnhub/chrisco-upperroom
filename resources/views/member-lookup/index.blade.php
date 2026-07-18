@extends('layouts.app')

@section('title', 'Verify My Details — Chrisco Upper Room Fellowship')

@section('content')

<section class="py-10" style="background: #0a1f44;">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <i class="fas fa-id-card text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-3xl font-bold text-white mb-2">Verify My Details</h1>
        <p class="text-gray-300">Enter your email address to view the information we have on record for you.</p>
    </div>
</section>

<section class="py-10 bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto px-4">

        {{-- Search Form --}}
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <form method="POST" action="{{ route('member.lookup.post') }}">
                @csrf
                <label class="block text-sm font-semibold text-gray-700 mb-2">Your Email Address</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="email" name="email" value="{{ old('email', session('lookup_email', request()->isMethod('post') ? request('email') : '')) }}"
                           placeholder="yourname@email.com" required autofocus
                           class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror">
                    <button type="submit" class="btn-red px-6 py-3 text-sm font-semibold w-full sm:w-auto">
                        <i class="fas fa-search mr-1"></i> Look Up
                    </button>
                </div>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </form>
        </div>


        {{-- Result --}}
        @if(isset($searched))
            @if($member)
            <div class="bg-white rounded-xl shadow overflow-hidden">
                {{-- Header --}}
                <div class="px-4 sm:px-6 py-4 sm:py-5 flex items-center gap-3 flex-wrap" style="background: #0a1f44;">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl font-bold flex-shrink-0" style="background: #c0392b;">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-base sm:text-xl truncate">
                            {{ $member->name }} {{ $member->middle_name }} {{ $member->last_name }}
                        </p>
                        <p class="text-yellow-400 text-xs sm:text-sm truncate">{{ $member->email }}</p>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full font-semibold flex-shrink-0" style="background: #f0a500; color: #0a1f44;">
                        {{ $member->role === 'admin' ? 'IT Support' : 'Member' }}
                    </span>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Personal --}}
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color: #c0392b;">Personal Information</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-gray-400 text-xs">First Name</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Middle Name</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->middle_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Last Name</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->last_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Gender</dt>
                                <dd class="text-gray-800 font-medium capitalize">{{ $member->gender ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Marital Status</dt>
                                <dd class="text-gray-800 font-medium capitalize">{{ $member->marital_status ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Date of Birth</dt>
                                <dd class="text-gray-800 font-medium">
                                    {{ $member->date_of_birth ? $member->date_of_birth->format('d M Y') : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Career / Occupation</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->occupation ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Contact & Location --}}
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color: #c0392b;">Contact & Location</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-gray-400 text-xs">Phone Number</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->phone ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Email Address</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">County of Residence</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->county ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Sub County</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->sub_county ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Sub Location / Estate</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->sub_location ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Physical Address</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->address ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Church Info --}}
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color: #c0392b;">Church Information</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div>
                                <dt class="text-gray-400 text-xs">Month & Year of Salvation</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->salvation_date ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Date of Joining CUR</dt>
                                <dd class="text-gray-800 font-medium">
                                    {{ $member->membership_date ? $member->membership_date->format('d M Y') : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Committed Member?</dt>
                                <dd class="font-medium">
                                    @if($member->is_committed_member)
                                        <span class="text-green-600">Yes</span>
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Month & Year Committed</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->committed_date ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Department(s)</dt>
                                <dd class="text-gray-800 font-medium">
                                    @php $depts = collect([$member->department, $member->department2, $member->department3])->filter()->values(); @endphp
                                    @if($depts->isNotEmpty())
                                        {{ $depts->implode(' · ') }}
                                    @else —@endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Office</dt>
                                <dd class="text-gray-800 font-medium capitalize">{{ $member->office ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Belongs to a Home Cell?</dt>
                                <dd class="font-medium">
                                    @if($member->belongs_to_home_cell)
                                        <span class="text-green-600">Yes</span>
                                        @if($member->home_cell)
                                            — <span class="text-gray-800">{{ $member->home_cell }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Assigned Deacon / Deaconess?</dt>
                                <dd class="font-medium">
                                    @if($member->assigned_to_deacon)
                                        <span class="text-green-600">Yes</span>
                                        @if($member->deacon_name)
                                            — <span class="text-gray-800">{{ $member->deacon_name }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">No</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Next of Kin --}}
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color: #c0392b;">Next of Kin (Emergency Contact)</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                            <div>
                                <dt class="text-gray-400 text-xs">Full Name</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->next_of_kin_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Relationship</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->next_of_kin_relationship ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-400 text-xs">Phone Number</dt>
                                <dd class="text-gray-800 font-medium">{{ $member->next_of_kin_phone ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Children --}}
                    @if(isset($children) && $children->count())
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color: #c0392b;">My Children</h2>
                        <div class="space-y-2">
                            @foreach($children as $child)
                            <div class="flex items-center gap-4 bg-gray-50 rounded-lg px-4 py-3 text-sm">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background: #0a1f44;">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $child->full_name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $child->date_of_birth ? $child->date_of_birth->format('d M Y') : '' }}
                                        {{ $child->gender ? ' · ' . ucfirst($child->gender) : '' }}
                                        {{ $child->sunday_school_class ? ' · ' . $child->sunday_school_class : '' }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Request Correction Button --}}
                    <div class="pt-2 border-t border-gray-100">
                        @if(session('correction_sent'))
                            <div class="mb-3 bg-green-50 border border-green-300 rounded-lg px-4 py-3 flex items-center gap-3 text-green-800 text-sm">
                                <i class="fas fa-check-circle text-green-500 text-lg flex-shrink-0"></i>
                                <div>
                                    <p class="font-semibold">Correction request submitted!</p>
                                    <p class="mt-0.5">The church office will review and update your records.</p>
                                </div>
                            </div>
                        @endif
                        <button onclick="document.getElementById('correction-modal').classList.remove('hidden')"
                            class="w-full py-2.5 rounded-lg font-semibold text-sm transition-opacity hover:opacity-80"
                            style="background: #f0a500; color: #0a1f44;">
                            <i class="fas fa-edit mr-2"></i>Request Correction
                        </button>
                    </div>

                </div>
            </div>

            {{-- Correction Request Modal --}}
            <div id="correction-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" style="background: rgba(0,0,0,0.55);">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                    <div class="px-6 py-4 border-b flex items-center justify-between" style="background: #0a1f44; border-radius: 0.75rem 0.75rem 0 0;">
                        <h3 class="text-white font-bold text-lg"><i class="fas fa-edit mr-2" style="color:#f0a500;"></i>Request Correction</h3>
                        <button onclick="document.getElementById('correction-modal').classList.add('hidden')" class="text-gray-300 hover:text-white text-xl leading-none">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('member.lookup.correction') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $member->email }}">
                        <div class="p-6">
                            <p class="text-sm text-gray-500 mb-4">Please describe which details are incorrect and what the correct information should be. The church office will update your records.</p>
                            <textarea name="message" rows="5" required minlength="10" maxlength="1000"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 @error('message') border-red-400 @enderror"
                                placeholder="e.g. My phone number is wrong, it should be 0712 345 678. Also my department should be Worship not Ushering.">{{ old('message') }}</textarea>
                            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="px-6 pb-6 flex gap-3">
                            <button type="submit"
                                class="flex-1 py-2.5 rounded-lg font-semibold text-sm transition-opacity hover:opacity-80"
                                style="background: #f0a500; color: #0a1f44;">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Request
                            </button>
                            <button type="button" onclick="document.getElementById('correction-modal').classList.add('hidden')"
                                class="px-5 py-2.5 rounded-lg font-semibold text-sm bg-gray-100 text-gray-600 hover:bg-gray-200">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @else
            {{-- Not found --}}
            <div class="bg-white rounded-xl shadow p-10 text-center">
                <i class="fas fa-user-slash text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-700 font-semibold text-lg mb-1">No record found</p>
                <p class="text-gray-400 text-sm mb-4">
                    We could not find a member with the email <strong>{{ old('email') }}</strong>.
                </p>
                <p class="text-sm text-gray-500">
                    If you believe this is an error, please contact the church office at <strong>0726900700</strong>.
                </p>
            </div>
            @endif
        @endif

    </div>
</section>

@push('scripts')
<script>
// Close modal on backdrop click
document.getElementById('correction-modal')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
@endpush

@endsection
