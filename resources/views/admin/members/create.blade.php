@extends('layouts.admin')

@section('title', 'Add Member')
@section('page-title', 'Add Member')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Add New Member</h1>
    <a href="{{ route('admin.members.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.members.store') }}" class="space-y-6">
@csrf

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Personal Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Member Type <span class="text-red-500">*</span></label>
            <select name="member_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="adult" {{ old('member_type', 'adult') === 'adult' ? 'selected' : '' }}>Adult</option>
                <option value="child" {{ old('member_type') === 'child' ? 'selected' : '' }}>Child (Sunday School)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Marital Status</label>
            <select name="marital_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="single" {{ old('marital_status') === 'single' ? 'selected' : '' }}>Single</option>
                <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>Married</option>
                <option value="widowed" {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>Divorced</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Career / Occupation</label>
            <input type="text" name="occupation" value="{{ old('occupation') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Contact & Location</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">County of Residence</label>
            <input type="text" name="county" value="{{ old('county') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sub County</label>
            <input type="text" name="sub_county" value="{{ old('sub_county') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Estate</label>
            <input type="text" name="sub_location" value="{{ old('sub_location') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Additional Directions</label>
            <input type="text" name="address" value="{{ old('address') }}"
                   placeholder="e.g. Near the market, blue gate"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Church Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Are you Born Again?</label>
            <select name="is_born_again" onchange="toggleField('salvation_date_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('is_born_again', '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_born_again') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="salvation_date_row" class="{{ old('is_born_again') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month &amp; Year of Salvation</label>
            <input type="text" name="salvation_date" value="{{ old('salvation_date') }}"
                   placeholder="e.g. March 2019"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Are you Baptized?
                <span class="font-normal text-gray-400 text-xs ml-1">(Full immersion on Many Waters)</span>
            </label>
            <select name="is_baptized" onchange="toggleField('baptism_date_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('is_baptized', '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_baptized') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="baptism_date_row" class="{{ old('is_baptized') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month &amp; Year of Baptism</label>
            <input type="text" name="baptism_date" value="{{ old('baptism_date') }}"
                   placeholder="e.g. June 2019"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Joining CUR</label>
            <input type="date" name="membership_date" value="{{ old('membership_date') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Are you a Committed Member?</label>
            <select name="commitment_status" onchange="toggleField('committed_date_row', this.value === 'committed')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="committed" {{ old('commitment_status') === 'committed'                        ? 'selected' : '' }}>Yes</option>
                <option value="in_class"  {{ old('commitment_status') === 'in_class'                         ? 'selected' : '' }}>No (Currently in Commitment Class)</option>
                <option value="none"      {{ old('commitment_status', 'none') === 'none'                     ? 'selected' : '' }}>No (Not in Commitment Class)</option>
            </select>
        </div>
        <div id="committed_date_row" class="{{ old('commitment_status') === 'committed' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month &amp; Year Committed</label>
            <input type="text" name="committed_date" value="{{ old('committed_date') }}"
                   placeholder="e.g. January 2020"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Transfer Status</label>
            <select name="transfer_type" id="transfer_type_create"
                    onchange="document.getElementById('transfer_church_wrap_create').style.display = this.value === 'in' ? 'block' : 'none'"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="" {{ old('transfer_type') == '' ? 'selected' : '' }}>Not Transferred</option>
                <option value="in" {{ old('transfer_type') == 'in' ? 'selected' : '' }}>Transferred In (from another Chrisco Church)</option>
            </select>
        </div>
        <div id="transfer_church_wrap_create" style="{{ old('transfer_type') === 'in' ? '' : 'display:none;' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Church Transferred From</label>
            <input type="text" name="transfer_church" value="{{ old('transfer_church') }}"
                   placeholder="e.g. Chrisco Upper Room Westlands"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 1</label>
            <input type="text" name="department" value="{{ old('department') }}"
                   placeholder="e.g. Worship"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 2 <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" name="department2" value="{{ old('department2') }}"
                   placeholder="e.g. Ushering"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 3 <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" name="department3" value="{{ old('department3') }}"
                   placeholder="e.g. Youth"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Do you belong to a Home Cell?</label>
            <select name="belongs_to_home_cell" id="belongs_home_cell" onchange="toggleField('home_cell_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('belongs_to_home_cell', '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('belongs_to_home_cell') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="home_cell_row" class="{{ old('belongs_to_home_cell') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, which Home Cell?</label>
            <input type="text" name="home_cell" value="{{ old('home_cell') }}"
                   placeholder="Name of home cell group"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Are you Assigned to a Deacon / Deaconess?</label>
            <select name="assigned_to_deacon" id="assigned_deacon" onchange="toggleField('deacon_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('assigned_to_deacon', '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('assigned_to_deacon') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="deacon_row" class="{{ old('assigned_to_deacon') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, Deacon / Deaconess Name</label>
            <input type="text" name="deacon_name" value="{{ old('deacon_name') }}"
                   placeholder="Full name"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Office</label>
            <select name="office" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— None —</option>
                <option value="presbyter"  {{ old('office') === 'presbyter'  ? 'selected' : '' }}>Presbyter</option>
                <option value="pastor"     {{ old('office') === 'pastor'     ? 'selected' : '' }}>Pastor</option>
                <option value="elder"      {{ old('office') === 'elder'      ? 'selected' : '' }}>Elder</option>
                <option value="deacon"     {{ old('office') === 'deacon'     ? 'selected' : '' }}>Deacon</option>
                <option value="deaconess"  {{ old('office') === 'deaconess'  ? 'selected' : '' }}>Deaconess</option>
            </select>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Next of Kin (Emergency Contacts)</h2>

    {{-- Contact 1 --}}
    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#f0a500;">Contact 1</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
            <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
            <input type="text" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship') }}"
                   placeholder="e.g. Spouse, Parent, Sibling"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>

    {{-- Contact 2 --}}
    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#f0a500;">Contact 2</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
            <input type="text" name="next_of_kin2_name" value="{{ old('next_of_kin2_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
            <input type="text" name="next_of_kin2_relationship" value="{{ old('next_of_kin2_relationship') }}"
                   placeholder="e.g. Spouse, Parent, Sibling"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="next_of_kin2_phone" value="{{ old('next_of_kin2_phone') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Medical Information --}}
<div class="rounded-xl shadow p-6" style="background:#fff; border-left: 4px solid #0a1f44;">
    <h2 class="font-bold text-lg mb-1 pb-2 border-b" style="color: #0a1f44;">
        <i class="fas fa-notes-medical mr-2" style="color:#f0a500;"></i>Medical Information
    </h2>
    <p class="text-xs text-gray-400 mb-5">This information is confidential and used only for the member's welfare and safety.</p>

    {{-- Medical condition toggle --}}
    <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Does this member have any underlying medical condition(s)?</label>
        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="radio" name="has_medical_condition" value="1" id="med-yes"
                    onchange="document.getElementById('medical-details').classList.remove('hidden')"
                    {{ old('has_medical_condition') == '1' ? 'checked' : '' }}>
                Yes
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="radio" name="has_medical_condition" value="0" id="med-no"
                    onchange="document.getElementById('medical-details').classList.add('hidden')"
                    {{ old('has_medical_condition', '0') == '0' ? 'checked' : '' }}>
                No
            </label>
        </div>
    </div>

    {{-- Condition details (shown only when Yes) --}}
    <div id="medical-details" class="{{ old('has_medical_condition') == '1' ? '' : 'hidden' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Condition(s) / Diagnosis</label>
                <textarea name="medical_conditions" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="e.g. Diabetes, Hypertension">{{ old('medical_conditions') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Medication(s) Currently Taking</label>
                <textarea name="medications" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="e.g. Metformin 500mg">{{ old('medications') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Always visible --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Allergies <span class="font-normal text-gray-400">(if any)</span></label>
            <input type="text" name="allergies" value="{{ old('allergies') }}"
                placeholder="e.g. Penicillin, Peanuts"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Emergency Medical Contact <span class="font-normal text-gray-400">(if different from Next of Kin)</span></label>
            <input type="text" name="emergency_medical_contact" value="{{ old('emergency_medical_contact') }}"
                placeholder="Full name"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Their Phone Number</label>
            <input type="text" name="emergency_medical_phone" value="{{ old('emergency_medical_phone') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div class="md:col-span-3">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Any Special Needs / Disability we should be aware of</label>
            <textarea name="special_needs" rows="2"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                placeholder="e.g. Wheelchair user, hearing impaired">{{ old('special_needs') }}</textarea>
        </div>
    </div>
</div>


<div class="flex gap-3">
    <button type="submit" class="btn-red px-8 py-2 font-semibold">
        <i class="fas fa-user-plus mr-2"></i>Add Member
    </button>
    <a href="{{ route('admin.members.index') }}" class="btn-navy px-6 py-2">Cancel</a>
</div>

</form>

@push('scripts')
<script>
function toggleField(id, show) {
    document.getElementById(id).classList.toggle('hidden', !show);
}
</script>
@endpush

@endsection


