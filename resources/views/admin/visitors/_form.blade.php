{{-- Shared form fields for create and edit --}}
@php $v = $visitor ?? null; @endphp

{{-- Consent (only show on create) --}}
@if(!$v)
<div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <label class="flex items-start gap-3 cursor-pointer">
        <input type="checkbox" name="consent" id="consent" value="1" required
               class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600">
        <span class="text-sm text-blue-900">
            <strong>Consent required:</strong>
            I consent to the church storing and using my information for ministry purposes.
        </span>
    </label>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    {{-- Full Name --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
        <input type="text" name="full_name" value="{{ old('full_name', $v?->full_name) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('full_name') border-red-400 @enderror"
               placeholder="Enter full name" required>
        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Gender --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
        <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Select gender</option>
            @foreach(['Male','Female','Other'] as $g)
            <option value="{{ $g }}" {{ old('gender', $v?->gender) == $g ? 'selected' : '' }}>{{ $g }}</option>
            @endforeach
        </select>
    </div>

    {{-- Marital Status --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Marital Status</label>
        <select name="marital_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Select status</option>
            @foreach(['Single','Married','Divorced','Widowed','Separated'] as $ms)
            <option value="{{ $ms }}" {{ old('marital_status', $v?->marital_status) == $ms ? 'selected' : '' }}>{{ $ms }}</option>
            @endforeach
        </select>
    </div>

    {{-- Residence --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Residence / Area</label>
        <input type="text" name="residence" value="{{ old('residence', $v?->residence) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="e.g. Nairobi, Westlands">
    </div>

    {{-- Occupation --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Occupation</label>
        <input type="text" name="occupation" value="{{ old('occupation', $v?->occupation) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="e.g. Teacher, Engineer">
    </div>

    {{-- Phone --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
        <input type="text" name="phone" value="{{ old('phone', $v?->phone) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('phone') border-red-400 @enderror"
               placeholder="e.g. 0712345678" required>
        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $v?->email) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="visitor@email.com">
    </div>

    {{-- Preferred Contact --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Preferred Contact Method</label>
        <div class="flex flex-wrap gap-4">
            @foreach(['phone'=>'Phone Call','whatsapp'=>'WhatsApp','sms'=>'SMS','email'=>'Email'] as $key=>$label)
            @php
                $checked = in_array($key, old('preferred_contact', $v?->preferred_contact ?? []));
            @endphp
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="preferred_contact[]" value="{{ $key }}"
                       {{ $checked ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-gray-300 text-blue-600">
                <span class="text-sm text-gray-700">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Visited Before --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Visited Before?</label>
        <div class="flex gap-4 mt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="visited_before" value="1"
                       {{ old('visited_before', $v?->visited_before ? '1' : '0') == '1' ? 'checked' : '' }}
                       class="text-blue-600">
                <span class="text-sm text-gray-700">Yes</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="visited_before" value="0"
                       {{ old('visited_before', $v?->visited_before ? '1' : '0') != '1' ? 'checked' : '' }}
                       class="text-blue-600">
                <span class="text-sm text-gray-700">No</span>
            </label>
        </div>
    </div>

    {{-- Chrisco Member --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Chrisco Member (another branch)?</label>
        <div class="flex gap-4 mt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_chrisco_member" value="1" id="chrisco_yes"
                       {{ old('is_chrisco_member', $v?->is_chrisco_member ? '1' : '0') == '1' ? 'checked' : '' }}
                       class="text-blue-600" onchange="toggleField('chrisco_church_wrap', this.value=='1')">
                <span class="text-sm text-gray-700">Yes</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="is_chrisco_member" value="0"
                       {{ old('is_chrisco_member', $v?->is_chrisco_member ? '1' : '0') != '1' ? 'checked' : '' }}
                       class="text-blue-600" onchange="toggleField('chrisco_church_wrap', this.value=='1')">
                <span class="text-sm text-gray-700">No</span>
            </label>
        </div>
    </div>

    {{-- Chrisco Church Name --}}
    <div id="chrisco_church_wrap" class="md:col-span-2 {{ old('is_chrisco_member', $v?->is_chrisco_member ? '1' : '0') != '1' ? 'hidden' : '' }}">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Chrisco Branch Name</label>
        <input type="text" name="chrisco_church" value="{{ old('chrisco_church', $v?->chrisco_church) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="e.g. Chrisco Upper Room Kasarani">
    </div>

    {{-- Attend Another Church --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Attends Another Church?</label>
        <div class="flex gap-4 mt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="attends_another_church" value="1"
                       {{ old('attends_another_church', $v?->attends_another_church ? '1' : '0') == '1' ? 'checked' : '' }}
                       class="text-blue-600" onchange="toggleField('another_church_wrap', this.value=='1')">
                <span class="text-sm text-gray-700">Yes</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="attends_another_church" value="0"
                       {{ old('attends_another_church', $v?->attends_another_church ? '1' : '0') != '1' ? 'checked' : '' }}
                       class="text-blue-600" onchange="toggleField('another_church_wrap', this.value=='1')">
                <span class="text-sm text-gray-700">No</span>
            </label>
        </div>
    </div>

    {{-- Another Church Name --}}
    <div id="another_church_wrap" class="{{ old('attends_another_church', $v?->attends_another_church ? '1' : '0') != '1' ? 'hidden' : '' }}">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Church Name</label>
        <input type="text" name="another_church_name" value="{{ old('another_church_name', $v?->another_church_name) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="e.g. PCEA Nairobi">
    </div>

    {{-- Visit Date --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Visit Date <span class="text-red-500">*</span></label>
        <input type="date" name="visit_date"
               value="{{ old('visit_date', $v?->visit_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 @error('visit_date') border-red-400 @enderror"
               required>
        @error('visit_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Invited By --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Invited By</label>
        <input type="text" name="invited_by" value="{{ old('invited_by', $v?->invited_by) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
               placeholder="Name of member who invited them">
    </div>

    {{-- How Heard --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">How Did You Hear About Us?</label>
        <select name="how_heard" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            <option value="">Select an option</option>
            @foreach(['Friend','Family','Social Media','Website','Walk In','Evangelism','Other'] as $src)
            <option value="{{ $src }}" {{ old('how_heard', $v?->how_heard) == $src ? 'selected' : '' }}>{{ $src }}</option>
            @endforeach
        </select>
    </div>

    @if($v)
    {{-- Follow-up Status (only on edit) --}}
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Follow-up Status</label>
        <select name="follow_up_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            @foreach(['pending'=>'Pending','contacted'=>'Contacted','completed'=>'Completed'] as $k=>$lbl)
            <option value="{{ $k }}" {{ old('follow_up_status', $v?->follow_up_status) == $k ? 'selected' : '' }}>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Prayer Request --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Prayer Request</label>
        <textarea name="prayer_request" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                  placeholder="Any prayer requests the visitor shared…">{{ old('prayer_request', $v?->prayer_request) }}</textarea>
    </div>

    {{-- Notes (admin only) --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Internal Notes <span class="text-xs text-gray-400">(not shared with visitor)</span></label>
        <textarea name="notes" rows="2"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                  placeholder="Follow-up notes, observations…">{{ old('notes', $v?->notes) }}</textarea>
    </div>

</div>

@push('scripts')
<script>
function toggleField(id, show) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden', !show);
}
</script>
@endpush
