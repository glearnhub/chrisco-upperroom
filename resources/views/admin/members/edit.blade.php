@extends('layouts.admin')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Edit: {{ $member->name }} {{ $member->last_name }}</h1>
    <a href="{{ route('admin.members.show', $member) }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.members.update', $member) }}" class="space-y-6">
@csrf @method('PUT')

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Personal Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $member->middle_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="male" {{ old('gender', $member->gender) === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $member->gender) === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Marital Status</label>
            <select name="marital_status" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="single" {{ old('marital_status', $member->marital_status) === 'single' ? 'selected' : '' }}>Single</option>
                <option value="married" {{ old('marital_status', $member->marital_status) === 'married' ? 'selected' : '' }}>Married</option>
                <option value="widowed" {{ old('marital_status', $member->marital_status) === 'widowed' ? 'selected' : '' }}>Widowed</option>
                <option value="divorced" {{ old('marital_status', $member->marital_status) === 'divorced' ? 'selected' : '' }}>Divorced</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth?->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Career / Occupation</label>
            <input type="text" name="occupation" value="{{ old('occupation', $member->occupation) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Contact & Location</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone', $member->phone) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-400 @enderror">
            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">County of Residence</label>
            <input type="text" name="county" value="{{ old('county', $member->county) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sub County</label>
            <input type="text" name="sub_county" value="{{ old('sub_county', $member->sub_county) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sub Location / Estate</label>
            <input type="text" name="sub_location" value="{{ old('sub_location', $member->sub_location) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Physical Address</label>
            <input type="text" name="address" value="{{ old('address', $member->address) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Church Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month &amp; Year of Salvation</label>
            <input type="text" name="salvation_date" value="{{ old('salvation_date', $member->salvation_date) }}"
                   placeholder="e.g. March 2019"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Joining CUR</label>
            <input type="date" name="membership_date" value="{{ old('membership_date', $member->membership_date?->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Are you a Committed Member?</label>
            <select name="is_committed_member" onchange="toggleField('committed_date_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('is_committed_member', $member->is_committed_member ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_committed_member', $member->is_committed_member ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="committed_date_row" class="{{ old('is_committed_member', $member->is_committed_member ? '1' : '0') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Month &amp; Year Committed</label>
            <input type="text" name="committed_date" value="{{ old('committed_date', $member->committed_date) }}"
                   placeholder="e.g. January 2020"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 1</label>
            <input type="text" name="department" value="{{ old('department', $member->department) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 2 <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" name="department2" value="{{ old('department2', $member->department2) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Department 3 <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" name="department3" value="{{ old('department3', $member->department3) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Do you belong to a Home Cell?</label>
            <select name="belongs_to_home_cell" onchange="toggleField('home_cell_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('belongs_to_home_cell', $member->belongs_to_home_cell ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('belongs_to_home_cell', $member->belongs_to_home_cell ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="home_cell_row" class="{{ old('belongs_to_home_cell', $member->belongs_to_home_cell ? '1' : '0') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, which Home Cell?</label>
            <input type="text" name="home_cell" value="{{ old('home_cell', $member->home_cell) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned to a Deacon / Deaconess?</label>
            <select name="assigned_to_deacon" onchange="toggleField('deacon_row', this.value === '1')"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="0" {{ old('assigned_to_deacon', $member->assigned_to_deacon ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('assigned_to_deacon', $member->assigned_to_deacon ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div id="deacon_row" class="{{ old('assigned_to_deacon', $member->assigned_to_deacon ? '1' : '0') == '1' ? '' : 'hidden' }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">If Yes, Deacon / Deaconess Name</label>
            <input type="text" name="deacon_name" value="{{ old('deacon_name', $member->deacon_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Office</label>
            <select name="office" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— None —</option>
                <option value="presbyter"  {{ old('office', $member->office) === 'presbyter'  ? 'selected' : '' }}>Presbyter</option>
                <option value="pastor"     {{ old('office', $member->office) === 'pastor'     ? 'selected' : '' }}>Pastor</option>
                <option value="elder"      {{ old('office', $member->office) === 'elder'      ? 'selected' : '' }}>Elder</option>
                <option value="deacon"     {{ old('office', $member->office) === 'deacon'     ? 'selected' : '' }}>Deacon</option>
                <option value="deaconess"  {{ old('office', $member->office) === 'deaconess'  ? 'selected' : '' }}>Deaconess</option>
            </select>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color: #0a1f44;">Next of Kin (Emergency Contact)</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
            <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name', $member->next_of_kin_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Relationship</label>
            <input type="text" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship', $member->next_of_kin_relationship) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
            <input type="text" name="next_of_kin_phone" value="{{ old('next_of_kin_phone', $member->next_of_kin_phone) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="btn-red px-8 py-2 font-semibold">
        <i class="fas fa-save mr-2"></i>Save Changes
    </button>
    <a href="{{ route('admin.members.show', $member) }}" class="btn-navy px-6 py-2">Cancel</a>
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


