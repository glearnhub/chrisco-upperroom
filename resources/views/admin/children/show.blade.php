@extends('layouts.admin')

@section('title', $child->full_name)
@section('page-title', 'Members (Children)')

@push('styles')
<style>
@media print {
    .sidebar, header, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
}
</style>
@endpush

@section('content')

<div class="flex items-center justify-between mb-6 no-print">
    <h1 class="text-2xl font-bold" style="color:#0a1f44;">{{ $child->full_name }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.children.edit', $child) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#f0a500;">
            <i class="fas fa-edit"></i> Edit
        </a>
        <button onclick="window.print()"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
                style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('admin.children.index') }}" class="btn-navy px-4 py-2 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-5 flex items-center gap-4" style="background:#0a1f44;">
        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-2xl font-bold flex-shrink-0"
             style="background:#c0392b;">
            {{ strtoupper(substr($child->first_name, 0, 1)) }}
        </div>
        <div>
            <p class="text-white font-bold text-xl">{{ $child->full_name }}</p>
            <p class="text-yellow-400 text-sm">
                {{ $child->sunday_school_class ?? 'No class assigned' }}
                @if($child->gender) &nbsp;·&nbsp; {{ ucfirst($child->gender) }} @endif
                @if($child->date_of_birth) &nbsp;·&nbsp; Born {{ $child->date_of_birth->format('d M Y') }} @endif
            </p>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- Personal --}}
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color:#c0392b;">Personal Information</h2>
            <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                <div><dt class="text-gray-400 text-xs">First Name</dt><dd class="font-medium text-gray-800">{{ $child->first_name }}</dd></div>
                <div><dt class="text-gray-400 text-xs">Middle Name</dt><dd class="font-medium text-gray-800">{{ $child->middle_name ?: '—' }}</dd></div>
                <div><dt class="text-gray-400 text-xs">Last Name</dt><dd class="font-medium text-gray-800">{{ $child->last_name }}</dd></div>
                <div><dt class="text-gray-400 text-xs">Gender</dt><dd class="font-medium text-gray-800 capitalize">{{ $child->gender ?: '—' }}</dd></div>
                <div><dt class="text-gray-400 text-xs">Date of Birth</dt><dd class="font-medium text-gray-800">{{ $child->date_of_birth ? $child->date_of_birth->format('d M Y') : '—' }}</dd></div>
            </dl>
        </div>

        {{-- Parents --}}
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color:#c0392b;">Parent / Guardian Information</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#0a1f44;">Parent 1</p>
                    <p class="font-semibold text-gray-800">{{ $child->parent1_display }}</p>
                    <p class="text-gray-500 text-sm mt-1">{{ $child->parent1_contact ?: '—' }}</p>
                    @if($child->parent1)
                        <span class="text-xs px-2 py-0.5 rounded-full mt-2 inline-block" style="background:#e8f0fe;color:#0a1f44;">
                            <i class="fas fa-link mr-1"></i>Registered Member
                        </span>
                    @endif
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#0a1f44;">Parent 2</p>
                    <p class="font-semibold text-gray-800">{{ $child->parent2_display }}</p>
                    <p class="text-gray-500 text-sm mt-1">{{ $child->parent2_contact ?: '—' }}</p>
                    @if($child->parent2)
                        <span class="text-xs px-2 py-0.5 rounded-full mt-2 inline-block" style="background:#e8f0fe;color:#0a1f44;">
                            <i class="fas fa-link mr-1"></i>Registered Member
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Church --}}
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider mb-3 pb-1 border-b" style="color:#c0392b;">Church Information</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-400 text-xs">Sunday School Class</dt>
                    <dd class="font-medium text-gray-800">
                        @if($child->sunday_school_class)
                            <span class="px-2 py-0.5 rounded-full text-sm font-semibold" style="background:#e8f0fe;color:#0a1f44;">
                                {{ $child->sunday_school_class }}
                            </span>
                        @else
                            —
                        @endif
                    </dd>
                </div>
                @if($child->notes)
                <div class="col-span-2">
                    <dt class="text-gray-400 text-xs">Notes</dt>
                    <dd class="font-medium text-gray-800">{{ $child->notes }}</dd>
                </div>
                @endif
            </dl>
        </div>

    </div>
</div>

@endsection


