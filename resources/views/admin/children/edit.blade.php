@extends('layouts.admin')

@section('title', 'Edit Child')
@section('page-title', 'Members (Children)')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">Edit: {{ $child->full_name }}</h1>
    <a href="{{ route('admin.children.show', $child) }}" class="btn-navy px-4 py-2 text-sm">
        <i class="fas fa-arrow-left mr-2"></i>Back
    </a>
</div>

<form method="POST" action="{{ route('admin.children.update', $child) }}" class="space-y-6">
@csrf @method('PUT')

{{-- Personal Information --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">Personal Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $child->first_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $child->middle_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $child->last_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select —</option>
                <option value="male"   {{ old('gender', $child->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $child->gender) === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $child->date_of_birth?->format('Y-m-d')) }}"
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
                            {{ old('parent1_id', $child->parent1_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Name</label>
            <input type="text" name="parent1_name" id="parent1_name"
                   value="{{ old('parent1_name', $child->parent1_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 1 Contact</label>
            <input type="text" name="parent1_contact" id="parent1_contact"
                   value="{{ old('parent1_contact', $child->parent1_contact) }}"
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
                            {{ old('parent2_id', $child->parent2_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Name</label>
            <input type="text" name="parent2_name" id="parent2_name"
                   value="{{ old('parent2_name', $child->parent2_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Parent 2 Contact</label>
            <input type="text" name="parent2_contact" id="parent2_contact"
                   value="{{ old('parent2_contact', $child->parent2_contact) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

{{-- Church --}}
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-lg mb-4 pb-2 border-b" style="color:#0a1f44;">Church Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Sunday School Class Level</label>
            <select name="sunday_school_class" class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">— Select class —</option>
                @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ old('sunday_school_class', $child->sunday_school_class) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes</label>
            <input type="text" name="notes" value="{{ old('notes', $child->notes) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" class="btn-red px-8 py-2 font-semibold">
        <i class="fas fa-save mr-2"></i>Save Changes
    </button>
    <a href="{{ route('admin.children.show', $child) }}" class="btn-navy px-6 py-2">Cancel</a>
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


