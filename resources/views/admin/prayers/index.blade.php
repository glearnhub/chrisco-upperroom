@extends('layouts.admin')

@section('title', 'Prayer Requests')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold" style="color:#0a1f44;">Prayer Requests</h1>
            @if(!auth()->user()->isSuperAdmin())
            <p class="text-sm text-gray-500 mt-0.5">Showing prayers assigned to you</p>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between gap-3 flex-wrap">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <div class="flex items-center gap-2">
            @if(session('whatsapp_url'))
            <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white"
               style="background:#25d366;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:13px;height:13px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp {{ session('whatsapp_name') }}
            </a>
            @endif
            <button onclick="this.closest('div.mb-4').remove()" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded flex items-center justify-between">
        <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 ml-4"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Statuses</option>
                <option value="ongoing"  {{ request('status')=='ongoing'  ? 'selected' : '' }}>Ongoing</option>
                <option value="pending"  {{ request('status')=='pending'  ? 'selected' : '' }}>Pending</option>
                <option value="prayed"   {{ request('status')=='prayed'   ? 'selected' : '' }}>Prayed</option>
                <option value="answered" {{ request('status')=='answered' ? 'selected' : '' }}>Answered</option>
            </select>

            @if(auth()->user()->isSuperAdmin())
            <select name="assigned" class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Assignments</option>
                <option value="unassigned" {{ request('assigned')=='unassigned' ? 'selected' : '' }}>Unassigned</option>
                @foreach($leaders as $leader)
                <option value="{{ $leader->id }}" {{ request('assigned')==$leader->id ? 'selected' : '' }}>
                    {{ trim($leader->name . ' ' . $leader->last_name) }}
                </option>
                @endforeach
            </select>
            @endif

            <button type="submit" class="px-4 py-2 text-sm rounded text-white font-semibold" style="background:#0a1f44;">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['status','assigned']))
            <a href="{{ route('admin.prayers.index') }}" class="px-3 py-2 text-sm rounded border border-gray-300 text-gray-600 hover:bg-gray-50">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($prayers->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="prayers-table">
                <thead style="background:#0a1f44;">
                    <tr>
                        @if(auth()->user()->isSuperAdmin())
                        <th class="px-4 py-3 w-10">
                            <input type="checkbox" id="select-all"
                                   class="h-4 w-4 rounded border-gray-300 cursor-pointer"
                                   title="Select all on this page">
                        </th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Request</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Status</th>
                        @if(auth()->user()->isSuperAdmin())
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Assigned To</th>
                        @endif
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white hidden lg:table-cell">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($prayers as $prayer)
                    <tr class="hover:bg-gray-50 prayer-row" data-id="{{ $prayer->id }}">

                        @if(auth()->user()->isSuperAdmin())
                        <td class="px-4 py-3">
                            <input type="checkbox" class="prayer-checkbox h-4 w-4 rounded border-gray-300 cursor-pointer"
                                   value="{{ $prayer->id }}">
                        </td>
                        @endif

                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">
                                {{ $prayer->is_anonymous ? 'Anonymous' : ($prayer->name ?: '—') }}
                            </div>
                            @if(!$prayer->is_anonymous && $prayer->email)
                            <div class="text-xs text-gray-400">{{ $prayer->email }}</div>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-gray-700">{{ $prayer->phone ?: '—' }}</td>

                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            <span title="{{ $prayer->request }}">{{ Str::limit($prayer->request, 90) }}</span>
                        </td>

                        <td class="px-4 py-3">
                            @php
                                $sc = match($prayer->status ?? 'pending') {
                                    'answered' => 'bg-green-100 text-green-700',
                                    'prayed'   => 'bg-blue-100 text-blue-700',
                                    'ongoing'  => 'bg-purple-100 text-purple-700',
                                    default    => 'bg-yellow-100 text-yellow-700',
                                };
                            @endphp
                            <span class="text-xs px-2 py-1 rounded-full {{ $sc }}">
                                {{ ucfirst($prayer->status ?? 'pending') }}
                            </span>
                        </td>

                        @if(auth()->user()->isSuperAdmin())
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.prayers.assign', $prayer) }}">
                                @csrf @method('PATCH')
                                <select name="assigned_to"
                                        onchange="this.form.submit()"
                                        class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-300 max-w-[180px]">
                                    <option value="">— Unassigned —</option>
                                    @foreach($leaders as $ldr)
                                    <option value="{{ $ldr->id }}"
                                            {{ $prayer->assigned_to == $ldr->id ? 'selected' : '' }}>
                                        {{ trim($ldr->name . ' ' . $ldr->last_name) }} ({{ ucfirst($ldr->office) }})
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                            @if($prayer->assignedLeader && $prayer->assignedLeader->phone)
                                @php
                                    $leaderPhone = preg_replace('/\D/', '', $prayer->assignedLeader->phone);
                                    // Normalize Kenyan numbers: 07xx → 2547xx, +254 already handled
                                    if (str_starts_with($leaderPhone, '0')) {
                                        $leaderPhone = '254' . substr($leaderPhone, 1);
                                    } elseif (str_starts_with($leaderPhone, '+')) {
                                        $leaderPhone = ltrim($leaderPhone, '+');
                                    }
                                    $waMsg = "Hello " . trim($prayer->assignedLeader->name . ' ' . $prayer->assignedLeader->last_name) . ",\n\n"
                                           . "You have been assigned a prayer request on the Chrisco Upper Room Fellowship system.\n\n"
                                           . "*Prayer Request:*\n"
                                           . wordwrap($prayer->request ?? $prayer->message ?? '', 80, "\n", true) . "\n\n"
                                           . "Please pray with and follow up on this person. God bless you!";
                                    $waUrl = 'https://wa.me/' . $leaderPhone . '?text=' . rawurlencode($waMsg);
                                @endphp
                                <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1 mt-1 px-2 py-1 rounded text-xs font-semibold"
                                   style="background:#25d366; color:#fff;"
                                   title="Send WhatsApp message to {{ trim($prayer->assignedLeader->name . ' ' . $prayer->assignedLeader->last_name) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px;height:12px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                </a>
                            @endif
                        </td>
                        @endif

                        <td class="px-4 py-3 text-gray-400 text-xs hidden lg:table-cell">
                            {{ \Carbon\Carbon::parse($prayer->created_at)->format('M d, Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.prayers.updateStatus', $prayer) }}" class="flex items-center gap-1">
                                @csrf @method('PATCH')
                                <select name="status" class="border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none">
                                    <option value="ongoing" {{ ($prayer->status ?? '') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="pending" {{ ($prayer->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="prayed"  {{ ($prayer->status ?? '') === 'prayed'  ? 'selected' : '' }}>Prayed</option>
                                </select>
                                <button type="submit" class="px-2 py-1 rounded text-xs text-white font-semibold hover:opacity-90" style="background:#0a1f44;">
                                    Save
                                </button>
                            </form>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100">{{ $prayers->withQueryString()->links() }}</div>
        @else
        <div class="text-center py-16 text-gray-400">
            <i class="fas fa-praying-hands text-5xl mb-3 block"></i>
            <p class="font-medium">
                No prayer requests
                @if(!auth()->user()->isSuperAdmin()) assigned to you @endif
                @if(request()->hasAny(['status','assigned'])) matching your filters @endif
                yet.
            </p>
        </div>
        @endif
    </div>
</div>

@if(auth()->user()->isSuperAdmin())

{{-- ── Floating selection bar ─────────────────────────────── --}}
<div id="bulk-bar"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden
            flex items-center gap-4 px-6 py-3 rounded-2xl shadow-2xl border border-blue-200"
     style="background:#0a1f44; min-width:340px;">
    <span class="text-white text-sm font-semibold">
        <i class="fas fa-check-square mr-2 text-yellow-400"></i>
        <span id="selected-count">0</span> selected
    </span>
    <button id="open-assign-modal"
            class="ml-auto inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold"
            style="background:#f0a500; color:#0a1f44;">
        <i class="fas fa-user-check"></i> Assign to Leader
    </button>
    <button id="deselect-all" title="Clear selection" class="text-gray-400 hover:text-white ml-1">
        <i class="fas fa-times"></i>
    </button>
</div>

{{-- ── Bulk assign modal ──────────────────────────────────── --}}
<div id="assign-modal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center"
     style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style="background:#0a1f44;">
            <h2 class="text-white font-bold text-lg">Assign Prayer Requests</h2>
            <button id="close-modal" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
        </div>

        <form method="POST" action="{{ route('admin.prayers.bulk-assign') }}" id="bulk-assign-form">
            @csrf
            <div id="hidden-ids"></div>

            <div class="px-6 py-6">
                <p class="text-sm text-gray-600 mb-4">
                    Assign <strong id="modal-count">0</strong> selected prayer request(s) to a leader.
                    An email with all details will be sent to the leader.
                </p>

                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Assign to <span class="text-red-500">*</span>
                </label>
                <select name="assigned_to" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">— Select a leader —</option>
                    @foreach($leaders as $ldr)
                    <option value="{{ $ldr->id }}">
                        {{ trim($ldr->name . ' ' . $ldr->last_name) }} — {{ ucfirst($ldr->office) }}
                    </option>
                    @endforeach
                </select>
                @if($leaders->isEmpty())
                <p class="text-xs text-red-500 mt-1">
                    No leaders with a qualifying office (Presbyter, Pastor, Elder, Deacon, Deaconess) found.
                </p>
                @endif
            </div>

            <div class="px-6 pb-6 flex items-center justify-between">
                <button type="button" id="cancel-modal"
                        class="text-sm text-gray-500 hover:underline">Cancel</button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold text-white"
                        style="background:#0a1f44;">
                    <i class="fas fa-paper-plane"></i> Assign &amp; Notify
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const checkboxes  = () => document.querySelectorAll('.prayer-checkbox');
    const selectAll   = document.getElementById('select-all');
    const bulkBar     = document.getElementById('bulk-bar');
    const countLabel  = document.getElementById('selected-count');
    const modalCount  = document.getElementById('modal-count');
    const hiddenIds   = document.getElementById('hidden-ids');
    const modal       = document.getElementById('assign-modal');

    function getChecked() {
        return [...checkboxes()].filter(c => c.checked);
    }

    function updateBar() {
        const checked = getChecked();
        const n = checked.length;
        countLabel.textContent = n;
        bulkBar.classList.toggle('hidden', n === 0);

        // Keep select-all in sync
        const all = checkboxes();
        selectAll.indeterminate = n > 0 && n < all.length;
        selectAll.checked = n > 0 && n === all.length;
    }

    // Row checkbox change
    document.getElementById('prayers-table').addEventListener('change', function (e) {
        if (e.target.classList.contains('prayer-checkbox') || e.target.id === 'select-all') {
            if (e.target.id === 'select-all') {
                checkboxes().forEach(c => c.checked = e.target.checked);
            }
            updateBar();
        }
    });

    // Deselect all
    document.getElementById('deselect-all').addEventListener('click', function () {
        checkboxes().forEach(c => c.checked = false);
        selectAll.checked = false;
        selectAll.indeterminate = false;
        updateBar();
    });

    // Open modal
    document.getElementById('open-assign-modal').addEventListener('click', function () {
        const checked = getChecked();
        // Populate hidden inputs
        hiddenIds.innerHTML = '';
        checked.forEach(c => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'prayer_ids[]';
            inp.value = c.value;
            hiddenIds.appendChild(inp);
        });
        modalCount.textContent = checked.length;
        modal.classList.remove('hidden');
    });

    // Close modal
    function closeModal() { modal.classList.add('hidden'); }
    document.getElementById('close-modal').addEventListener('click', closeModal);
    document.getElementById('cancel-modal').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
})();
</script>
@endpush

@endif

@endsection
