@extends('layouts.admin')

@section('title', 'Usher Check-In')
@section('page-title', 'Usher Check-In')

@push('styles')
<style>
/* Full dark mobile panel */
.usher-panel {
    background: #0a1f44;
    border-radius: 20px;
    overflow: hidden;
    max-width: 420px;
    margin: 0 auto;
    min-height: 85vh;
    display: flex;
    flex-direction: column;
    color: #fff;
    font-family: inherit;
}
.usher-header {
    padding: 20px 20px 16px;
    background: linear-gradient(135deg, #0a1f44 0%, #112952 100%);
}
.usher-tag {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .12em;
    color: #f0a500;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.usher-title {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.2;
}
.live-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #16a34a;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 99px;
    letter-spacing: .05em;
}
.live-dot {
    width: 6px; height: 6px;
    background: #fff;
    border-radius: 50%;
    animation: pulse 1.4s infinite;
}
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
.usher-date {
    font-size: 12px;
    color: rgba(255,255,255,.55);
    margin-top: 5px;
}
.stats-row {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 10px;
    padding: 16px 20px 0;
}
.stat-card {
    background: rgba(255,255,255,.07);
    border-radius: 12px;
    padding: 12px 8px;
    text-align: center;
}
.stat-num {
    font-size: 26px;
    font-weight: 800;
    color: #f0a500;
    line-height: 1;
}
.stat-label {
    font-size: 10px;
    color: rgba(255,255,255,.45);
    margin-top: 4px;
    font-weight: 600;
    letter-spacing: .04em;
}
.search-wrap {
    padding: 16px 20px 0;
}
.search-input-wrap {
    position: relative;
}
.search-input-wrap i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,.35);
    font-size: 13px;
}
#search-box {
    width: 100%;
    background: rgba(255,255,255,.1);
    border: 1.5px solid rgba(255,255,255,.15);
    border-radius: 12px;
    padding: 11px 14px 11px 38px;
    color: #fff;
    font-size: 14px;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
#search-box::placeholder { color: rgba(255,255,255,.35); }
#search-box:focus { border-color: #f0a500; }
#search-box:disabled { opacity: .4; cursor: not-allowed; }

.results-area {
    padding: 10px 20px 0;
    flex-shrink: 0;
}
.result-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 12px;
    margin-bottom: 6px;
    cursor: pointer;
    transition: background .15s;
    background: rgba(255,255,255,.06);
}
.result-row.checked { background: rgba(240,165,0,.12); cursor: default; }
.result-row:not(.checked):hover { background: rgba(255,255,255,.11); }

.avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    color: #f0a500;
    flex-shrink: 0;
    text-transform: uppercase;
    overflow: hidden;
}
.avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

.result-name { font-size: 14px; font-weight: 700; color: #fff; }
.result-sub  { font-size: 11px; color: rgba(255,255,255,.45); margin-top: 1px; }

.in-badge {
    margin-left: auto;
    flex-shrink: 0;
    background: rgba(240,165,0,.2);
    color: #f0a500;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    white-space: nowrap;
}
.checkin-btn {
    margin-left: auto;
    flex-shrink: 0;
    background: #f0a500;
    color: #0a1f44;
    font-size: 11px;
    font-weight: 800;
    padding: 5px 12px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    white-space: nowrap;
}
.checkin-btn:hover { background: #d4920a; }

.divider-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    color: rgba(255,255,255,.3);
    text-transform: uppercase;
    padding: 12px 20px 6px;
}

.recent-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 20px 20px;
}
.recent-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.recent-meta { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 2px; }
.recent-time {
    margin-left: auto;
    flex-shrink: 0;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.35);
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}
.undo-btn {
    background: none;
    border: none;
    color: rgba(255,100,100,.6);
    font-size: 11px;
    cursor: pointer;
    padding: 0;
    font-weight: 600;
}
.undo-btn:hover { color: #f87171; }

.closed-notice {
    margin: 0 20px 12px;
    background: rgba(240,165,0,.15);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 12px;
    color: #f0a500;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Confirm Modal ───────────────────────── */
.confirm-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
    backdrop-filter: blur(3px);
}
.confirm-overlay.hidden { display: none; }
.confirm-box {
    background: #0d1f3c;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 18px;
    padding: 28px 24px 22px;
    width: 100%;
    max-width: 320px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
}
.confirm-box .c-icon {
    width: 52px; height: 52px;
    border-radius: 50%;
    background: rgba(248,113,113,.12);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 14px;
    font-size: 20px;
    color: #f87171;
}
.confirm-box h3 {
    font-size: 16px; font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
}
.confirm-box p {
    font-size: 13px;
    color: rgba(255,255,255,.45);
    margin-bottom: 22px;
    line-height: 1.5;
}
.confirm-actions { display: flex; gap: 10px; }
.confirm-actions button {
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
.confirm-actions button:hover { opacity: .85; }
.btn-cancel-modal {
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.6);
}
.btn-confirm-undo {
    background: #ef4444;
    color: #fff;
}
</style>
@endpush

@section('content')

{{-- Back link outside the panel --}}
<div class="mb-4 max-w-[420px] mx-auto">
    <a href="{{ route('admin.attendance.index') }}" class="text-sm text-gray-400 hover:text-gray-200 inline-flex items-center gap-1">
        <i class="fas fa-arrow-left text-xs"></i> Back to Attendance
    </a>
</div>

<div class="usher-panel shadow-2xl">

    {{-- Header --}}
    <div class="usher-header">
        <div class="usher-tag">Chrisco Upper Room &middot; Usher</div>
        <div class="usher-title">
            Check-In
            @if($session->isOpen())
            <span class="live-badge"><span class="live-dot"></span> LIVE</span>
            @else
            <span style="font-size:11px;font-weight:600;background:rgba(255,255,255,.1);padding:3px 10px;border-radius:99px;color:rgba(255,255,255,.5);">CLOSED</span>
            @endif
        </div>
        <div class="usher-date">
            {{ $session->service_date->format('l, d M Y') }}
            @if($session->opened_at) &nbsp;&middot;&nbsp; {{ $session->opened_at->format('H:i') }}@if($session->closed_at)&ndash;{{ $session->closed_at->format('H:i') }}@endif @endif
        </div>
    </div>

    {{-- Stats --}}
    @php $present = $session->attendanceCount(); $notYet = max(0, $totalMembers - $present); $pct = $totalMembers > 0 ? round($present/$totalMembers*100) : 0; @endphp
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-num" id="total-count">{{ $present }}</div>
            <div class="stat-label">Present</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" id="not-yet-count">{{ $notYet }}</div>
            <div class="stat-label">Not yet</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" id="pct-count">{{ $pct }}%</div>
            <div class="stat-label">Arrived</div>
        </div>
    </div>

    {{-- Closed notice --}}
    @if(!$session->isOpen())
    <div style="margin: 12px 20px 0;">
        <div class="closed-notice">
            <i class="fas fa-lock"></i> Session closed. Viewing records only.
        </div>
    </div>
    @endif

    {{-- Search --}}
    <div class="search-wrap">
        <div class="search-input-wrap">
            <i class="fas fa-search"></i>
            <input id="search-box" type="text" autocomplete="off"
                   placeholder="Name or phone…"
                   {{ $session->isOpen() ? '' : 'disabled' }}>
        </div>
    </div>

    {{-- Search results --}}
    <div class="results-area" id="search-results-wrap" style="display:none;">
        <div id="search-results"></div>
    </div>

    {{-- Divider --}}
    <div class="divider-label">Just Checked In</div>

    {{-- Recent check-ins --}}
    <div class="recent-list" id="recent-list">
        @forelse($attendances as $a)
        @php $initials = $a->member ? strtoupper(substr($a->member->name,0,1).substr($a->member->last_name??'',0,1)) : '?'; @endphp
        <div class="recent-row" id="entry-{{ $a->id }}">
            <div class="avatar">
                @if($a->member && $a->member->profile_photo)
                    <img src="{{ asset('storage/'.$a->member->profile_photo) }}">
                @else
                    {{ $initials }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="result-name" style="font-size:13px;">{{ $a->member?->full_name }}</div>
                <div class="recent-meta">{{ ucfirst($a->method) }} &middot; Door {{ strtoupper($a->door) }}</div>
            </div>
            <div class="recent-time">
                <span>{{ $a->checked_in_at->format('H:i') }}</span>
                @if($session->isOpen())
                <button class="undo-btn" onclick="undoCheckin({{ $a->id }})"><i class="fas fa-undo-alt"></i></button>
                @endif
            </div>
        </div>
        @empty
        <p style="text-align:center;color:rgba(255,255,255,.25);font-size:13px;padding:24px 0;" id="empty-msg">No check-ins yet.</p>
        @endforelse
    </div>

</div>

{{-- Undo confirm modal --}}
<div class="confirm-overlay hidden" id="undo-modal">
    <div class="confirm-box">
        <div class="c-icon"><i class="fas fa-undo-alt"></i></div>
        <h3>Remove Check-In?</h3>
        <p>This will undo the check-in record.<br>The member can check in again.</p>
        <div class="confirm-actions">
            <button class="btn-cancel-modal" onclick="closeUndoModal()">Cancel</button>
            <button class="btn-confirm-undo" id="btn-confirm-undo">Remove</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const SESSION_ID   = {{ $session->id }};
const CAN_EDIT     = {{ $session->isOpen() ? 'true' : 'false' }};
const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
const TOTAL_MEMBERS= {{ $totalMembers }};
let searchTimer    = null;

const box        = document.getElementById('search-box');
const resultsWrap= document.getElementById('search-results-wrap');
const results    = document.getElementById('search-results');
const countEl    = document.getElementById('total-count');
const notYetEl   = document.getElementById('not-yet-count');
const pctEl      = document.getElementById('pct-count');

function updateCounts(presentCount) {
    countEl.textContent  = presentCount;
    const notYet = Math.max(0, TOTAL_MEMBERS - presentCount);
    notYetEl.textContent = notYet;
    pctEl.textContent    = TOTAL_MEMBERS > 0 ? Math.round(presentCount / TOTAL_MEMBERS * 100) + '%' : '0%';
}

if (box) {
    box.addEventListener('input', () => {
        clearTimeout(searchTimer);
        const q = box.value.trim();
        if (q.length < 2) { resultsWrap.style.display = 'none'; return; }
        results.innerHTML = '<p style="text-align:center;color:rgba(255,255,255,.3);font-size:13px;padding:10px 0;">Searching…</p>';
        resultsWrap.style.display = 'block';
        searchTimer = setTimeout(() => doSearch(q), 300);
    });
    box.addEventListener('blur', () => {
        setTimeout(() => { if (!box.value.trim()) resultsWrap.style.display = 'none'; }, 200);
    });
}

function initials(name) {
    const parts = name.trim().split(' ').filter(Boolean);
    return (parts[0]?.[0] || '') + (parts[parts.length-1]?.[0] || '');
}

async function doSearch(q) {
    try {
        const r = await fetch(`/admin/attendance/session/${SESSION_ID}/search?q=${encodeURIComponent(q)}`, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const d = await r.json();
        updateCounts(d.total_in);
        if (!d.members.length) {
            results.innerHTML = '<p style="text-align:center;color:rgba(255,255,255,.3);font-size:13px;padding:10px 0;">No members found.</p>';
            return;
        }
        results.innerHTML = d.members.map(m => {
            const ini = initials(m.name).toUpperCase();
            const avatar = m.photo
                ? `<div class="avatar"><img src="${m.photo}"></div>`
                : `<div class="avatar">${ini}</div>`;
            const badge = m.already_in
                ? `<span class="in-badge"><i class="fas fa-check" style="margin-right:3px;"></i>In</span>`
                : (CAN_EDIT ? `<button class="checkin-btn" onclick="checkIn(${m.id}, event)"><i class="fas fa-check" style="margin-right:3px;"></i>In</button>` : '');
            return `<div class="result-row ${m.already_in ? 'checked' : ''}" id="res-${m.id}">
                ${avatar}
                <div style="flex:1;min-width:0;">
                    <div class="result-name">${m.name}</div>
                    <div class="result-sub">${m.department || m.phone || ''}</div>
                </div>
                ${badge}
            </div>`;
        }).join('');
    } catch(e) {
        results.innerHTML = '<p style="text-align:center;color:#f87171;font-size:13px;padding:10px 0;">Error. Try again.</p>';
    }
}

async function checkIn(userId, evt) {
    if (evt) evt.stopPropagation();
    try {
        const r = await fetch(`/admin/attendance/session/${SESSION_ID}/checkin`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ user_id: userId }),
        });
        const d = await r.json();
        if (!r.ok) return;
        updateCounts(d.total_in);
        const row = document.getElementById(`res-${userId}`);
        if (row) {
            row.classList.add('checked');
            const btn = row.querySelector('.checkin-btn');
            if (btn) btn.outerHTML = `<span class="in-badge"><i class="fas fa-check" style="margin-right:3px;"></i>In</span>`;
        }
        const emptyMsg = document.getElementById('empty-msg');
        if (emptyMsg) emptyMsg.remove();
        const list = document.getElementById('recent-list');
        const ini  = initials(d.entry.name).toUpperCase();
        const el   = document.createElement('div');
        el.id        = `entry-${d.entry.id}`;
        el.className = 'recent-row';
        el.innerHTML = `
            <div class="avatar">${ini}</div>
            <div style="flex:1;min-width:0;">
                <div class="result-name" style="font-size:13px;">${d.entry.name}</div>
                <div class="recent-meta">Usher &middot; Door Usher</div>
            </div>
            <div class="recent-time">
                <span>${d.entry.checked_in_at}</span>
                <button class="undo-btn" onclick="undoCheckin(${d.entry.id})"><i class="fas fa-undo-alt"></i></button>
            </div>`;
        list.prepend(el);
    } catch(e) {}
}

let pendingUndoId = null;

function undoCheckin(id) {
    pendingUndoId = id;
    document.getElementById('undo-modal').classList.remove('hidden');
}

function closeUndoModal() {
    pendingUndoId = null;
    document.getElementById('undo-modal').classList.add('hidden');
}

document.getElementById('btn-confirm-undo').addEventListener('click', async function() {
    if (!pendingUndoId) return;
    const id = pendingUndoId;
    closeUndoModal();
    try {
        const r = await fetch(`/admin/attendance/checkin/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        const d = await r.json();
        if (!r.ok) return;
        updateCounts(d.total_in);
        const el = document.getElementById(`entry-${id}`);
        if (el) el.remove();
        if (box.value.trim().length >= 2) doSearch(box.value.trim());
    } catch(e) {}
});

// Close on overlay click
document.getElementById('undo-modal').addEventListener('click', function(e) {
    if (e.target === this) closeUndoModal();
});
</script>
@endpush
