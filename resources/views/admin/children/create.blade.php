@extends('layouts.admin')

@section('title', 'Add Child')
@section('page-title', 'Members (Children)')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Add Child Record</h1>
    <a href="{{ route('admin.children.index') }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.children.store') }}" class="space-y-6">
@csrf

{{-- Personal Information --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">Personal Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('first_name') border-red-400 @enderror">
            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 @error('last_name') border-red-400 @enderror">
            @error('last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Parent 1 --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-user mr-2" style="color:#c0392b;"></i>Parent / Guardian 1
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Registered Member</label>
            <select name="parent1_id" id="parent1_id" onchange="fillParent(1, this)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Not a registered member —</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-phone="{{ $m->phone }}"
                            {{ old('parent1_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mt-1">Select to auto-fill name & contact below.</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Name</label>
            <input type="text" name="parent1_name" id="parent1_name" value="{{ old('parent1_name') }}"
                   placeholder="Full name"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Contact</label>
            <input type="text" name="parent1_contact" id="parent1_contact" value="{{ old('parent1_contact') }}"
                   placeholder="Phone number"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Parent 2 --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-user mr-2" style="color:#f0a500;"></i>Parent / Guardian 2 <span class="text-gray-400 text-sm font-normal">(optional)</span>
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Select Registered Member</label>
            <select name="parent2_id" id="parent2_id" onchange="fillParent(2, this)"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Not a registered member —</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" data-name="{{ $m->name }}" data-phone="{{ $m->phone }}"
                            {{ old('parent2_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Name</label>
            <input type="text" name="parent2_name" id="parent2_name" value="{{ old('parent2_name') }}"
                   placeholder="Full name"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Contact</label>
            <input type="text" name="parent2_contact" id="parent2_contact" value="{{ old('parent2_contact') }}"
                   placeholder="Phone number"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Church Information --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">
        <i class="fas fa-church mr-2" style="color:#c0392b;"></i>Church Information
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sunday School Class Level</label>
            <select name="sunday_school_class" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select class —</option>
                @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ old('sunday_school_class') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
            <input type="text" name="notes" value="{{ old('notes') }}"
                   placeholder="Any additional notes…"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="btn-red px-8 py-2 font-semibold">
        <i class="fas fa-child mr-2"></i>Add Child
    </button>
    <a href="{{ route('admin.children.index') }}" class="btn-navy px-6 py-2">Cancel</a>
</div>

</form>

@push('scripts')
<script>
function fillParent(n, select) {
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
        document.getElementById('parent' + n + '_name').value    = opt.dataset.name || '';
        document.getElementById('parent' + n + '_contact').value = opt.dataset.phone || '';
    }
}
</script>
@endpush

@endsection


