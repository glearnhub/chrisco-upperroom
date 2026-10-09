@extends('layouts.admin')
@section('title', 'Children Check-In')
@section('page-title', 'Children Check-In')

@push('styles')
<style>
.child-card { transition: transform .15s, box-shadow .15s; }
.child-card:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.08); }
.checked-in-row { animation: slideIn .3s ease; }
@keyframes slideIn { from { opacity:0; transform:translateX(-8px); } to { opacity:1; transform:translateX(0); } }
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold" style="color:#0a1f44;">Children Check-In</h1>
            <p class="text-sm text-gray-500 mt-0.5" id="date-display">
                {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
            </p>
        </div>
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('admin.children.attendance.report') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-xs sm:text-sm rounded border border-gray-300 bg-white text-gray-700">
                <i class="fas fa-chart-bar"></i> <span class="hidden sm:inline">Report</span>
            </a>
            <a href="{{ route('admin.children.attendance.history') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-xs sm:text-sm rounded border border-gray-300 bg-white text-gray-700">
                <i class="fas fa-history"></i> <span class="hidden sm:inline">History</span>
            </a>
            <a href="{{ route('admin.children.index') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-xs sm:text-sm rounded border border-gray-300 bg-white text-gray-700">
                <i class="fas fa-arrow-left"></i> <span class="hidden sm:inline">Children</span>
            </a>
        </div>
    </div>

    {{-- Date + Class filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Date</label>
                <input type="date" id="att-date" value="{{ $date }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Class</label>
                <select id="att-class"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="">All Classes</option>
                    @foreach($classes as $key => $label)
                    <option value="{{ $key }}" {{ $class === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="w-full px-4 py-2 rounded-lg text-center font-bold text-white text-sm" style="background:#0a1f44;">
                    <span id="checked-in-count">{{ $alreadyMarked->count() }}</span> checked in today
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- LEFT: Search & check-in --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-bold text-gray-800 mb-4"><i class="fas fa-search mr-2 text-blue-600"></i>Find & Check In</h2>

            <div class="relative mb-4">
                <input type="text" id="search-input" placeholder="Type child's name…"
                       class="w-full border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                       autocomplete="off">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <div id="search-results" class="space-y-2 min-h-[60px]">
                <p class="text-sm text-gray-400 text-center py-6" id="search-placeholder">
                    <i class="fas fa-child text-2xl block mb-2"></i>
                    Start typing to search children
                </p>
            </div>
        </div>

        {{-- RIGHT: Already checked in --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800"><i class="fas fa-check-circle mr-2 text-green-600"></i>Checked In</h2>
                <span id="present-badge"
                      class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                    {{ $alreadyMarked->count() }} present
                </span>
            </div>

            <div id="checked-list" class="space-y-2 flex-1 overflow-y-auto" style="max-height:420px;">
                @forelse($alreadyMarked as $rec)
                <div class="checked-in-row flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2"
                     id="row-{{ $rec->id }}">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-7 h-7 rounded-full bg-green-200 flex items-center justify-center flex-shrink-0 text-green-700 text-xs font-bold">
                            {{ strtoupper(substr($rec->child->first_name ?? '?', 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $rec->child->full_name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $rec->child->sunday_school_class ?? '—' }}</p>
                        </div>
                    </div>
                    <button onclick="undoCheckin({{ $rec->id }}, this)"
                            class="text-red-400 hover:text-red-600 flex-shrink-0 ml-2 text-xs" title="Undo">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @empty
                <div id="empty-checked" class="text-center text-gray-400 py-10">
                    <i class="fas fa-user-clock text-3xl mb-2 block"></i>
                    <p class="text-sm">No children checked in yet</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
const SEARCH_URL  = '{{ route('admin.children.attendance.search') }}';
const CHECKIN_URL = '{{ route('admin.children.attendance.checkin') }}';
const UNDO_BASE   = '{{ url('/admin/children/attendance/undo/') }}/';
const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

let searchTimer = null;

function getDate()  { return document.getElementById('att-date').value; }
function getClass() { return document.getElementById('att-class').value; }

// ── Search ──────────────────────────────────────────────────────────
document.getElementById('search-input').addEventListener('input', function () {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (!q) {
        document.getElementById('search-results').innerHTML =
            '<p class="text-sm text-gray-400 text-center py-6"><i class="fas fa-child text-2xl block mb-2"></i>Start typing to search children</p>';
        return;
    }
    searchTimer = setTimeout(() => doSearch(q), 280);
});

document.getElementById('att-date').addEventListener('change', () => {
    const q = document.getElementById('search-input').value.trim();
    if (q) doSearch(q);
    reloadCheckedList();
});
document.getElementById('att-class').addEventListener('change', () => {
    const q = document.getElementById('search-input').value.trim();
    if (q) doSearch(q);
    reloadCheckedList();
});

async function doSearch(q) {
    const url = `${SEARCH_URL}?q=${encodeURIComponent(q)}&date=${getDate()}&class=${encodeURIComponent(getClass())}`;
    const res  = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const list = await res.json();

    const container = document.getElementById('search-results');
    if (!list.length) {
        container.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No children found</p>';
        return;
    }

    container.innerHTML = list.map(c => `
        <div class="child-card flex items-center justify-between rounded-lg border px-3 py-2 gap-2
                    ${c.already_in ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200 hover:border-blue-300'}"
             id="sr-${c.id}">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 text-blue-700 text-xs font-bold">
                    ${c.name.charAt(0).toUpperCase()}
                </span>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate">${c.name}</p>
                    <p class="text-xs text-gray-500">${c.class}</p>
                </div>
            </div>
            ${c.already_in
                ? '<span class="text-xs font-semibold text-green-600 flex-shrink-0"><i class="fas fa-check mr-1"></i>Present</span>'
                : `<button onclick="doCheckin(${c.id}, '${c.name.replace(/'/g,"\\'")}', this)"
                           class="px-3 py-1 rounded text-xs font-bold text-white flex-shrink-0" style="background:#0a1f44;">
                       <i class="fas fa-plus mr-1"></i>Check In
                   </button>`
            }
        </div>`).join('');
}

// ── Check In ────────────────────────────────────────────────────────
async function doCheckin(childId, name, btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

    try {
        const res  = await fetch(CHECKIN_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ child_id: childId, date: getDate() }),
        });
        const data = await res.json();

        if (res.status === 409) { btn.innerHTML = '<i class="fas fa-check"></i> Present'; return; }
        if (!data.success) throw new Error();

        // Mark search card as present
        const srCard = document.getElementById('sr-' + childId);
        if (srCard) {
            const btnWrap = srCard.querySelector('button');
            if (btnWrap) btnWrap.outerHTML = '<span class="text-xs font-semibold text-green-600 flex-shrink-0"><i class="fas fa-check mr-1"></i>Present</span>';
            srCard.className = srCard.className.replace('bg-gray-50 border-gray-200 hover:border-blue-300', 'bg-green-50 border-green-200');
        }

        // Add to checked-in list
        addToCheckedList(data.id, data.name, data.class);
        updateCount(1);
    } catch(e) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus mr-1"></i>Check In';
    }
}

function addToCheckedList(recId, name, cls) {
    document.getElementById('empty-checked')?.remove();
    const list = document.getElementById('checked-list');
    const row  = document.createElement('div');
    row.id = 'row-' + recId;
    row.className = 'checked-in-row flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2';
    row.innerHTML = `
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-7 h-7 rounded-full bg-green-200 flex items-center justify-center flex-shrink-0 text-green-700 text-xs font-bold">
                ${name.charAt(0).toUpperCase()}
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-gray-800 text-sm truncate">${name}</p>
                <p class="text-xs text-gray-500">${cls}</p>
            </div>
        </div>
        <button onclick="undoCheckin(${recId}, this)" class="text-red-400 hover:text-red-600 flex-shrink-0 ml-2 text-xs" title="Undo">
            <i class="fas fa-times"></i>
        </button>`;
    list.prepend(row);
}

// ── Undo ────────────────────────────────────────────────────────────
async function undoCheckin(recId, btn) {
    if (!confirm('Remove this check-in?')) return;
    btn.disabled = true;
    try {
        await fetch(UNDO_BASE + recId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        document.getElementById('row-' + recId)?.remove();
        updateCount(-1);
        if (!document.getElementById('checked-list').querySelector('[id^="row-"]')) {
            document.getElementById('checked-list').innerHTML =
                '<div id="empty-checked" class="text-center text-gray-400 py-10"><i class="fas fa-user-clock text-3xl mb-2 block"></i><p class="text-sm">No children checked in yet</p></div>';
        }
        // Re-run search to reflect undo
        const q = document.getElementById('search-input').value.trim();
        if (q) doSearch(q);
    } catch(e) { btn.disabled = false; }
}

// ── Helpers ─────────────────────────────────────────────────────────
function updateCount(delta) {
    const countEl = document.getElementById('checked-in-count');
    const badgeEl = document.getElementById('present-badge');
    const n = parseInt(countEl.textContent) + delta;
    countEl.textContent = n;
    badgeEl.textContent = n + ' present';
}

async function reloadCheckedList() {
    // Simple page reload to reflect date/class filter change on right panel
    const url = new URL(window.location.href);
    url.searchParams.set('date', getDate());
    url.searchParams.set('class', getClass());
    window.location.href = url.toString();
}
</script>
@endpush
