@extends('layouts.admin')

@section('title', 'Event Check-In')
@section('page-title', 'Event Usher Check-In')

@push('styles')
<style>
.result-row { border:2px solid #e2e8f0; border-radius:10px; padding:10px 14px; }
.result-row.checked { border-color:#bbf7d0; background:#f0fdf4; }
.result-row.unchecked { cursor:pointer; }
.result-row.unchecked:hover { border-color:#0a1f44; background:#f8fafc; }
.result-row.unregistered { border-color:#fde68a; background:#fffbeb; cursor:pointer; }
.result-row.unregistered:hover { border-color:#f59e0b; }
#search-box:focus { outline:none; border-color:#0a1f44; box-shadow:0 0 0 3px rgba(10,31,68,.1); }

/* ── Undo confirm modal ─────────────────── */
.ev-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
}
.ev-overlay.hidden { display: none; }
.ev-box {
    background: #fff;
    border-radius: 18px;
    padding: 28px 24px 22px;
    width: 100%;
    max-width: 320px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}
.ev-box .ev-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: #fee2e2;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
    font-size: 20px;
    color: #ef4444;
}
.ev-box h3 { font-size: 16px; font-weight: 800; color: #0a1f44; margin-bottom: 6px; }
.ev-box p  { font-size: 13px; color: #6b7280; margin-bottom: 22px; line-height: 1.5; }
.ev-actions { display: flex; gap: 10px; }
.ev-actions button {
    flex: 1;
    padding: 11px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    border: none;
    transition: opacity .15s;
}
.ev-actions button:hover { opacity: .85; }
.ev-btn-cancel  { background: #f3f4f6; color: #374151; }
.ev-btn-confirm { background: #ef4444; color: #fff; }
</style>
@endpush

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.events.show', $event) }}" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
        <i class="fas fa-arrow-left text-xs"></i> Back to Event
    </a>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold" style="color:#0a1f44;">{{ $event->title }}</h1>
            <p class="text-gray-500 text-sm">{{ $event->start_datetime->format('D, d M Y · g:i A') }}</p>
        </div>
        <div class="flex gap-3 items-center">
            <div class="text-center px-5 py-3 rounded-xl" style="background:#0a1f44;min-width:90px;">
                <p class="text-3xl font-extrabold" style="color:#f0a500;" id="attended-count">{{ $attended }}</p>
                <p class="text-xs" style="color:rgba(255,255,255,.6);">attended</p>
            </div>
            <div class="text-center px-5 py-3 rounded-xl bg-gray-100" style="min-width:90px;">
                <p class="text-3xl font-extrabold text-gray-600">{{ $total }}</p>
                <p class="text-xs text-gray-400">registered</p>
            </div>
        </div>
    </div>
</div>

@if(!$isOpen)
<div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#fef9c3;color:#92400e;">
    <i class="fas fa-info-circle mr-2"></i>
    {{ $event->start_datetime->gt(now()) ? 'Event hasn\'t started yet. Check-in opens 2 hours before start.' : 'This event has ended. Viewing attendance records only.' }}
</div>
@endif

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#15803d;">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Search & check-in --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="text-base font-bold mb-4" style="color:#0a1f44;"><i class="fas fa-search mr-2 text-gray-400"></i>Search &amp; Check In</h2>

        <div class="relative mb-4">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input id="search-box" type="text" autocomplete="off"
                   placeholder="Name or phone…"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm"
                   {{ $isOpen ? '' : 'disabled' }}>
        </div>

        <div id="search-results" class="flex flex-col gap-2 min-h-[40px]">
            <p class="text-sm text-gray-400 text-center py-3">Type to search…</p>
        </div>

        {{-- Walk-in form (hidden by default) --}}
        <div id="walkin-form" class="hidden mt-4 border-t border-gray-100 pt-4">
            <h3 class="text-sm font-bold mb-3" style="color:#92400e;"><i class="fas fa-user-plus mr-1"></i> Walk-in Registration</h3>
            <input type="hidden" id="walkin-member-id">
            <div class="flex flex-col gap-3 mb-3">
                <input id="walkin-name"  type="text" placeholder="Full name *"   class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:border-blue-500">
                <input id="walkin-phone" type="tel"  placeholder="Phone number *" class="px-4 py-2 rounded-xl border border-gray-300 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div id="walkin-error" class="hidden mb-2 px-3 py-2 rounded-lg text-xs" style="background:#fee2e2;color:#dc2626;"></div>
            <div class="flex gap-2">
                <button onclick="closeWalkin()" class="flex-1 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">Cancel</button>
                <button id="walkin-btn" onclick="submitWalkin()"
                        class="flex-1 py-2 rounded-xl text-sm font-semibold text-white"
                        style="background:#0a1f44;">
                    Register &amp; Check In
                </button>
            </div>
        </div>

        <div id="search-error" class="hidden mt-3 px-3 py-2 rounded-lg text-sm" style="background:#fee2e2;color:#dc2626;"></div>
    </div>

    {{-- Attended list --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
        <h2 class="text-base font-bold mb-4" style="color:#0a1f44;"><i class="fas fa-check-circle mr-2 text-green-500"></i>Checked In</h2>
        <div id="attended-list" class="flex flex-col gap-1 max-h-96 overflow-y-auto">
            @forelse($attendedList as $reg)
            <div class="flex items-center gap-3 py-2 border-b border-gray-50" id="aentry-{{ $reg->id }}">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:#0a1f44;">{{ $reg->full_name }}</p>
                    <p class="text-xs text-gray-400">{{ $reg->attended_at?->format('H:i') }} · {{ ucfirst($reg->category) }}</p>
                </div>
                @if($isOpen)
                <button onclick="confirmUndo({{ $reg->id }})" class="text-xs text-red-400 hover:text-red-600 font-semibold flex-shrink-0">Undo</button>
                @endif
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4" id="empty-attended-msg">No one checked in yet.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Undo confirm modal --}}
<div class="ev-overlay hidden" id="ev-undo-modal">
    <div class="ev-box">
        <div class="ev-icon"><i class="fas fa-undo-alt"></i></div>
        <h3>Remove Check-In?</h3>
        <p>This will mark the attendee as not attended.<br>You can check them in again.</p>
        <div class="ev-actions">
            <button class="ev-btn-cancel" onclick="closeEvUndoModal()">Cancel</button>
            <button class="ev-btn-confirm" id="ev-btn-confirm">Remove</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const EVENT_ID = {{ $event->id }};
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const IS_OPEN  = {{ $isOpen ? 'true' : 'false' }};
let searchTimer   = null;
let pendingUndoId = null;

function confirmUndo(regId) {
    pendingUndoId = regId;
    document.getElementById('ev-undo-modal').classList.remove('hidden');
}
function closeEvUndoModal() {
    pendingUndoId = null;
    document.getElementById('ev-undo-modal').classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ev-btn-confirm').addEventListener('click', function() {
        if (!pendingUndoId) return;
        const id = pendingUndoId;
        closeEvUndoModal();
        toggleAttendance(id, false);
    });
    document.getElementById('ev-undo-modal').addEventListener('click', function(e) {
        if (e.target === this) closeEvUndoModal();
    });
});

const box    = document.getElementById('search-box');
const results= document.getElementById('search-results');
const errDiv = document.getElementById('search-error');
const countEl= document.getElementById('attended-count');

if (box) {
    box.addEventListener('input', () => {
        clearTimeout(searchTimer);
        const q = box.value.trim();
        if (q.length < 2) { results.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Type to search…</p>'; closeWalkin(); return; }
        results.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Searching…</p>';
        searchTimer = setTimeout(() => doSearch(q), 300);
    });
}

async function doSearch(q) {
    errDiv.classList.add('hidden');
    try {
        const r = await fetch(`/admin/events/${EVENT_ID}/checkin/search?q=${encodeURIComponent(q)}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const d = await r.json();
        if (!r.ok) { results.innerHTML = `<p class="text-sm text-red-500 text-center py-3">${d.error||'Error'}</p>`; return; }

        let html = '';

        d.registered.forEach(reg => {
            html += `<div class="result-row ${reg.attended ? 'checked' : 'unchecked'}" id="res-${reg.id}">
                <div class="flex items-center gap-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color:#0a1f44;">${reg.full_name}</p>
                        <p class="text-xs text-gray-400">${reg.phone||''} · ${ucFirst(reg.category)}</p>
                    </div>
                    ${reg.attended
                        ? `<span class="text-xs font-semibold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check mr-1"></i>${reg.attended_at ? reg.attended_at.substring(11,16) : ''}</span>`
                        : (IS_OPEN ? `<button onclick="toggleAttendance(${reg.id}, true)" class="text-xs font-semibold px-3 py-1.5 rounded-lg" style="background:#0a1f44;color:#f0a500;"><i class="fas fa-check mr-1"></i>Check In</button>` : '')
                    }
                </div>
            </div>`;
        });

        if (d.unregistered && d.unregistered.length && IS_OPEN) {
            html += `<p class="text-xs font-semibold text-amber-600 mt-2 mb-1"><i class="fas fa-info-circle mr-1"></i>Not yet registered:</p>`;
            d.unregistered.forEach(m => {
                html += `<div class="result-row unregistered" onclick="openWalkin('${escJ(m.name)}','${escJ(m.phone||'')}',${m.id})">
                    <div class="flex items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color:#92400e;">${m.name}</p>
                            <p class="text-xs text-gray-400">${m.phone||''}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background:#fef3c7;color:#92400e;">Register &amp; In</span>
                    </div>
                </div>`;
            });
        }

        if (!html && IS_OPEN) {
            html = `<div class="text-center py-3">
                <p class="text-sm text-gray-500 mb-2">No results found.</p>
                <button onclick="openWalkin('','',null)" class="text-xs font-semibold px-3 py-1.5 rounded-xl" style="background:#fef3c7;color:#92400e;"><i class="fas fa-user-plus mr-1"></i>Walk-in</button>
            </div>`;
        } else if (!html) {
            html = '<p class="text-sm text-gray-400 text-center py-3">No results found.</p>';
        }

        results.innerHTML = html;
    } catch(e) {
        results.innerHTML = '<p class="text-sm text-red-500 text-center py-3">Error. Try again.</p>';
    }
}

async function toggleAttendance(regId, markAs) {
    errDiv.classList.add('hidden');
    try {
        const r = await fetch(`/admin/events/${EVENT_ID}/checkin/mark/${regId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ attended: markAs }),
        });
        const d = await r.json();
        if (!r.ok) { errDiv.textContent = d.error||'Failed.'; errDiv.classList.remove('hidden'); return; }

        // Update count
        const count = parseInt(countEl.textContent) + (markAs ? 1 : -1);
        countEl.textContent = count;

        // Refresh search results row
        const row = document.getElementById(`res-${regId}`);
        if (row) {
            const time = d.attended_at ? d.attended_at.substring(0,5) : '';
            if (markAs) {
                row.classList.remove('unchecked'); row.classList.add('checked');
                const btn = row.querySelector('button');
                if (btn) btn.outerHTML = `<span class="text-xs font-semibold px-2 py-1 rounded-full" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check mr-1"></i>${time}</span>`;
            }
        }

        // Update attended list
        if (markAs) {
            const emptyMsg = document.getElementById('empty-attended-msg');
            if (emptyMsg) emptyMsg.remove();
            const list = document.getElementById('attended-list');
            const el = document.createElement('div');
            el.id = `aentry-${regId}`;
            el.className = 'flex items-center gap-3 py-2 border-b border-gray-50';
            el.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold truncate" style="color:#0a1f44;">${d.full_name || ''}</p>
                    <p class="text-xs text-gray-400">${time}</p>
                </div>
                <button onclick="confirmUndo(${regId})" class="text-xs text-red-400 hover:text-red-600 font-semibold flex-shrink-0">Undo</button>`;
            list.prepend(el);
        } else {
            const el = document.getElementById(`aentry-${regId}`);
            if (el) el.remove();
            // Refresh search results
            if (box.value.trim().length >= 2) doSearch(box.value.trim());
        }
    } catch(e) { errDiv.textContent = 'Network error.'; errDiv.classList.remove('hidden'); }
}

function openWalkin(name, phone, memberId) {
    document.getElementById('walkin-name').value      = name;
    document.getElementById('walkin-phone').value     = phone;
    document.getElementById('walkin-member-id').value = memberId || '';
    document.getElementById('walkin-error').classList.add('hidden');
    document.getElementById('walkin-form').classList.remove('hidden');
}

function closeWalkin() { document.getElementById('walkin-form').classList.add('hidden'); }

async function submitWalkin() {
    const name     = document.getElementById('walkin-name').value.trim();
    const phone    = document.getElementById('walkin-phone').value.trim();
    const memberId = document.getElementById('walkin-member-id').value;
    const errEl    = document.getElementById('walkin-error');
    const btn      = document.getElementById('walkin-btn');

    if (!name || !phone) { errEl.textContent = 'Name and phone required.'; errEl.classList.remove('hidden'); return; }

    btn.disabled = true; btn.textContent = 'Registering…';
    errEl.classList.add('hidden');

    try {
        const r = await fetch(`/admin/events/${EVENT_ID}/checkin/walkin`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ full_name: name, phone, member_id: memberId || null }),
        });
        const d = await r.json();
        if (!r.ok) { errEl.textContent = d.message || d.error || 'Failed.'; errEl.classList.remove('hidden'); btn.disabled = false; btn.textContent = 'Register & Check In'; return; }

        countEl.textContent = parseInt(countEl.textContent) + 1;
        const emptyMsg = document.getElementById('empty-attended-msg');
        if (emptyMsg) emptyMsg.remove();
        const list = document.getElementById('attended-list');
        const now  = new Date().toTimeString().slice(0,5);
        const el   = document.createElement('div');
        el.className = 'flex items-center gap-3 py-2 border-b border-gray-50';
        el.innerHTML = `<div class="flex-1 min-w-0"><p class="text-sm font-semibold truncate" style="color:#0a1f44;">${name}</p><p class="text-xs text-gray-400">${now} · Walk-in</p></div>`;
        list.prepend(el);
        closeWalkin();
        box.value = '';
        results.innerHTML = '<p class="text-sm text-green-600 text-center py-3"><i class="fas fa-check mr-1"></i>' + name + ' registered and checked in.</p>';
        btn.disabled = false; btn.textContent = 'Register & Check In';
    } catch(e) { errEl.textContent = 'Network error.'; errEl.classList.remove('hidden'); btn.disabled = false; btn.textContent = 'Register & Check In'; }
}

function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
function escJ(s) { return (s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"'); }
</script>
@endpush
