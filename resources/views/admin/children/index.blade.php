@extends('layouts.admin')

@section('title', 'Children')
@section('page-title', 'Members (Children)')

@push('styles')
<style>
@media print {
    .sidebar, header, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: white !important; }
    table { font-size: 11px; }
}
</style>
@endpush

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4 no-print">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Members (Children)</h1>
        <p class="text-gray-500 text-sm mt-1">Sunday School & children records</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.children.create') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#c0392b;">
            <i class="fas fa-plus"></i> Add Child
        </a>
        <a href="{{ route('admin.children.index', array_merge(request()->query(), ['export'=>'excel'])) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#1d6f42;">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('admin.children.import') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#7c3aed;">
            <i class="fas fa-file-upload"></i> Import Excel
        </a>
        <a href="{{ route('admin.children.print', request()->query()) }}" target="_blank"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#0a1f44;">
            <i class="fas fa-print"></i> Print
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow p-4 mb-4 no-print">
    <form method="GET" action="{{ route('admin.children.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or parent…"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div>
            <select name="class" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">All Classes</option>
                @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ request('class') === $key ? 'selected' : '' }}>{{ $key }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="gender" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none">
                <option value="">All Genders</option>
                <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#0a1f44;">
            <i class="fas fa-search mr-1"></i> Filter
        </button>
        @if(request()->hasAny(['search','class','gender']))
        <a href="{{ route('admin.children.index') }}" class="px-4 py-2 rounded-lg text-sm text-gray-600 border border-gray-300 hover:bg-gray-50">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="px-6 py-3 border-b flex items-center justify-between">
        <span class="text-sm text-gray-500">{{ $children->total() }} children found</span>
        <span class="text-xs text-gray-400">Page {{ $children->currentPage() }} of {{ $children->lastPage() }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">#</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Full Name</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Gender</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Class</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Parent 1</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Contact 1</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Parent 2</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold">Contact 2</th>
                    <th class="px-4 py-3 text-left text-xs text-gray-500 font-semibold no-print">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($children as $i => $child)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $children->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ $child->first_name }} {{ $child->middle_name }} {{ $child->last_name }}
                    </td>
                    <td class="px-4 py-3 capitalize text-gray-600">{{ $child->gender ?: '—' }}</td>
                    <td class="px-4 py-3">
                        @if($child->sunday_school_class)
                            <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                                  style="background:#e8f0fe;color:#0a1f44;">
                                {{ $child->sunday_school_class }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $child->parent1_display }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $child->parent1_contact ?: '—' }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $child->parent2_display }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $child->parent2_contact ?: '—' }}</td>
                    <td class="px-4 py-3 no-print">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.children.show', $child) }}"
                               class="text-xs px-2 py-1 rounded font-semibold text-white" style="background:#0a1f44;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.children.edit', $child) }}"
                               class="text-xs px-2 py-1 rounded font-semibold text-white" style="background:#f0a500;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.children.destroy', $child) }}"
                                  data-confirm="Delete {{ $child->first_name }}?" data-confirm-ok="Delete">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-2 py-1 rounded font-semibold text-white"
                                        style="background:#c0392b;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-child text-4xl mb-3 block"></i>
                        No children records found.
                        <a href="{{ route('admin.children.create') }}" class="text-blue-600 hover:underline ml-1">Add the first one.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($children->hasPages())
    <div class="px-6 py-4 border-t no-print">
        {{ $children->links() }}
    </div>
    @endif
</div>

@endsection


