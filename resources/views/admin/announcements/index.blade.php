@extends('layouts.admin')

@section('title', 'Announcements')
@section('page-title', 'Announcements')

@section('content')

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold" style="color: #0a1f44;">Announcements</h1>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.announcements.print') }}" target="_blank"
           class="px-4 py-2 text-sm rounded-lg border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 font-medium transition">
            <i class="fas fa-print mr-2"></i>Print
        </a>
        <a href="{{ route('admin.announcements.create') }}" class="btn-red px-4 py-2 text-sm">
            <i class="fas fa-plus mr-2"></i>Add Announcement
        </a>
    </div>
</div>

{{-- Bulk Actions Bar --}}
<div id="bulk-bar" class="hidden mb-4 bg-white border border-gray-200 rounded-xl shadow px-5 py-3 flex items-center gap-3 flex-wrap">
    <span id="bulk-count" class="text-sm font-semibold text-gray-700"></span>
    <form method="POST" action="{{ route('admin.announcements.bulk') }}" id="bulk-form">
        @csrf
        <div id="bulk-ids"></div>
        <input type="hidden" name="action" id="bulk-action">
        <div class="flex gap-2 flex-wrap">
            <button type="button" onclick="submitBulk('activate')"
                class="px-3 py-1.5 rounded text-xs font-semibold bg-green-100 text-green-700 hover:bg-green-200">
                <i class="fas fa-toggle-on mr-1"></i>Activate Selected
            </button>
            <button type="button" onclick="submitBulk('deactivate')"
                class="px-3 py-1.5 rounded text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200">
                <i class="fas fa-toggle-off mr-1"></i>Deactivate Selected
            </button>
            <button type="button" onclick="submitBulk('reset_expiry')"
                class="px-3 py-1.5 rounded text-xs font-semibold bg-purple-100 text-purple-700 hover:bg-purple-200">
                <i class="fas fa-sync-alt mr-1"></i>Reactivate & Clear Expiry
            </button>
        </div>
    </form>
    <button onclick="clearSelection()" class="ml-auto text-xs text-gray-400 hover:text-gray-600">
        <i class="fas fa-times mr-1"></i>Clear selection
    </button>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        @if($announcements->count())
            <table class="w-full text-sm">
                <thead style="background: #0a1f44;">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white w-8">
                            <input type="checkbox" id="select-all" onchange="toggleAll(this)" class="w-4 h-4 cursor-pointer">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Flags</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Expires</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($announcements as $ann)
                        @php $catColors = \App\Models\Announcement::CATEGORY_COLORS; @endphp
                        <tr class="hover:bg-gray-50 {{ $ann->is_pinned ? 'bg-yellow-50' : '' }} {{ !$ann->is_published ? 'opacity-60' : '' }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $ann->id }}"
                                    class="row-check w-4 h-4 cursor-pointer" onchange="updateBulkBar()">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($ann->image)
                                        <img src="{{ asset('storage/' . $ann->image) }}" class="w-10 h-10 rounded object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded flex items-center justify-center flex-shrink-0" style="background:#f1f5f9;">
                                            <i class="fas fa-bullhorn text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $ann->title }}</p>
                                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($ann->created_at)->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-bold px-2 py-1 rounded-full text-white"
                                    style="background: {{ $catColors[$ann->category] ?? '#0a1f44' }};">
                                    {{ ucfirst($ann->category) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{-- Quick toggle --}}
                                <form method="POST" action="{{ route('admin.announcements.toggle', $ann) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="text-xs px-3 py-1 rounded-full font-semibold transition-colors
                                        {{ $ann->is_published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                        title="{{ $ann->is_published ? 'Click to deactivate' : 'Click to activate' }}">
                                        <i class="fas fa-{{ $ann->is_published ? 'toggle-on' : 'toggle-off' }} mr-1"></i>
                                        {{ $ann->is_published ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @if($ann->is_pinned)
                                        <span class="text-xs px-1.5 py-0.5 rounded" style="background:#fef3c7; color:#92400e;" title="Pinned">
                                            <i class="fas fa-thumbtack"></i>
                                        </span>
                                    @endif
                                    @if($ann->is_recurring)
                                        <span class="text-xs px-1.5 py-0.5 rounded" style="background:#ede9fe; color:#6d28d9;" title="Recurring">
                                            <i class="fas fa-sync-alt"></i> Recurring
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($ann->expires_at)
                                    <span class="{{ $ann->expires_at->isPast() ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                        {{ $ann->expires_at->format('M d, Y') }}
                                        @if($ann->expires_at->isPast())
                                            <br><span class="text-red-400">Expired</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">No Expiry</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.announcements.edit', $ann) }}"
                                        class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 rounded text-xs font-medium">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $ann) }}"
                                        data-confirm="Delete this announcement?" data-confirm-ok="Delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-2 py-1 rounded text-xs font-medium">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $announcements->links() }}</div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-bullhorn text-5xl mb-3 block"></i>
                <p>No announcements yet. <a href="{{ route('admin.announcements.create') }}" class="text-blue-600 underline">Create one</a>.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulk-bar');
    document.getElementById('bulk-count').textContent = checked.length + ' selected';
    bar.classList.toggle('hidden', checked.length === 0);
    // uncheck master if not all selected
    const all = document.querySelectorAll('.row-check');
    document.getElementById('select-all').checked = checked.length === all.length && all.length > 0;
}

function clearSelection() {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    updateBulkBar();
}

function submitBulk(action) {
    const checked = document.querySelectorAll('.row-check:checked');
    if (!checked.length) return;

    const form = document.getElementById('bulk-form');
    const container = document.getElementById('bulk-ids');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('bulk-action').value = action;
    form.submit();
}
</script>
@endpush

@endsection
