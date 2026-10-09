<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Sunday Check-In — Chrisco Upper Room</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Inter', sans-serif;
    background: #08152e;
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: env(safe-area-inset-top, 0) 0 env(safe-area-inset-bottom, 0);
}

.phone {
    width: 100%;
    max-width: 420px;
    min-height: 100vh;
    background: #0c1d3a;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

/* ── Header ─────────────────────────────── */
.header {
    background: linear-gradient(160deg, #0e2248 0%, #091730 100%);
    padding: 20px 20px 18px;
    flex-shrink: 0;
}
.church-tag {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .14em;
    color: #f0a500;
    text-transform: uppercase;
    margin-bottom: 4px;
}
.service-name {
    font-size: 20px;
    font-weight: 800;
    color: #fff;
    line-height: 1.15;
}
.service-meta {
    font-size: 12px;
    color: rgba(255,255,255,.45);
    margin-top: 4px;
    font-weight: 500;
}

/* ── Screens ────────────────────────────── */
.screen { display: none; flex: 1; flex-direction: column; padding: 28px 20px 24px; }
.screen.active { display: flex; }

/* ── Screen 1: Search ───────────────────── */
.prompt {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    text-align: center;
    line-height: 1.35;
    margin-bottom: 22px;
}
.search-wrap {
    position: relative;
    margin-bottom: 14px;
}
.search-wrap i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,.3);
    font-size: 14px;
    pointer-events: none;
}
#search-input {
    width: 100%;
    background: rgba(255,255,255,.08);
    border: 1.5px solid rgba(255,255,255,.12);
    border-radius: 14px;
    padding: 14px 16px 14px 42px;
    color: #fff;
    font-size: 16px;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
}
#search-input::placeholder { color: rgba(255,255,255,.3); }
#search-input:focus { border-color: #f0a500; }

.hint {
    text-align: center;
    font-size: 12px;
    color: rgba(255,255,255,.28);
    line-height: 1.6;
    margin-top: 6px;
}

/* ── Screen 2: Results ──────────────────── */
.sub-prompt {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,.6);
    margin-bottom: 12px;
}
.search-input-sm {
    width: 100%;
    background: rgba(255,255,255,.08);
    border: 1.5px solid #f0a500;
    border-radius: 12px;
    padding: 11px 14px;
    color: #fff;
    font-size: 15px;
    font-family: inherit;
    outline: none;
    margin-bottom: 14px;
}
.result-list { display: flex; flex-direction: column; gap: 6px; }
.result-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 12px 14px;
    border-radius: 14px;
    background: rgba(255,255,255,.05);
    cursor: pointer;
    transition: background .15s;
    border: 1.5px solid transparent;
}
.result-item:hover, .result-item:active { background: rgba(255,255,255,.1); border-color: rgba(240,165,0,.4); }
.result-item.selected { background: rgba(240,165,0,.12); border-color: #f0a500; }

.avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(240,165,0,.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 800;
    color: #f0a500;
    flex-shrink: 0;
    text-transform: uppercase;
    overflow: hidden;
}
.avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.avatar.green { background: rgba(34,197,94,.2); color: #4ade80; }

.item-name { font-size: 15px; font-weight: 700; color: #fff; }
.item-sub  { font-size: 12px; color: rgba(255,255,255,.4); margin-top: 2px; }

.bottom-hint {
    text-align: center;
    font-size: 12px;
    color: rgba(255,255,255,.25);
    margin-top: 16px;
    line-height: 1.5;
}

/* ── Screen 3: Confirm ──────────────────── */
.confirm-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(240,165,0,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; font-weight: 900;
    color: #f0a500;
    margin: 0 auto 18px;
    overflow: hidden;
    text-transform: uppercase;
}
.confirm-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.confirm-name {
    font-size: 22px; font-weight: 800;
    color: #fff;
    text-align: center;
    margin-bottom: 6px;
}
.confirm-dept {
    font-size: 13px;
    color: rgba(255,255,255,.45);
    text-align: center;
    margin-bottom: 28px;
}
.confirm-question {
    font-size: 15px;
    font-weight: 600;
    color: rgba(255,255,255,.7);
    text-align: center;
    margin-bottom: 18px;
}
.btn-yes {
    width: 100%;
    background: rgba(255,255,255,.08);
    border: 1.5px solid rgba(255,255,255,.15);
    border-radius: 14px;
    padding: 15px;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .15s, border-color .15s;
    margin-bottom: 14px;
}
.btn-yes:hover { background: rgba(240,165,0,.15); border-color: #f0a500; }
.btn-back {
    background: none; border: none;
    color: rgba(255,255,255,.4);
    font-size: 14px; font-weight: 500;
    font-family: inherit;
    cursor: pointer;
    display: block;
    width: 100%;
    text-align: center;
    padding: 8px;
}
.btn-back:hover { color: rgba(255,255,255,.7); }

/* ── Screen 4: Success ──────────────────── */
.success-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: #16a34a;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 0 0 8px rgba(22,163,74,.2);
}
.success-icon i { font-size: 30px; color: #fff; }
.success-heading {
    font-size: 24px; font-weight: 800;
    color: #fff;
    text-align: center;
    margin-bottom: 6px;
}
.success-name {
    font-size: 22px; font-weight: 800;
    color: #f0a500;
    text-align: center;
    margin-bottom: 8px;
}
.success-meta {
    font-size: 13px;
    color: rgba(255,255,255,.4);
    text-align: center;
    margin-bottom: 32px;
}
.scripture {
    background: rgba(255,255,255,.05);
    border-left: 3px solid rgba(240,165,0,.5);
    border-radius: 0 12px 12px 0;
    padding: 14px 16px;
    font-size: 13px;
    font-style: italic;
    color: rgba(255,255,255,.5);
    line-height: 1.6;
}
.scripture cite {
    display: block;
    font-style: normal;
    font-weight: 700;
    color: rgba(240,165,0,.6);
    margin-top: 6px;
    font-size: 12px;
}
.btn-again {
    margin-top: 24px;
    background: none;
    border: 1.5px solid rgba(255,255,255,.12);
    border-radius: 12px;
    padding: 12px;
    color: rgba(255,255,255,.4);
    font-size: 13px;
    font-family: inherit;
    cursor: pointer;
    width: 100%;
}
.btn-again:hover { border-color: rgba(255,255,255,.25); color: rgba(255,255,255,.6); }

/* ── Already-in screen ──────────────────── */
.already-icon {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: rgba(240,165,0,.2);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
}
.already-icon i { font-size: 28px; color: #f0a500; }

/* ── No session ─────────────────────────── */
.no-session {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 40px 24px;
    text-align: center;
}
.no-session i { font-size: 40px; color: rgba(255,255,255,.15); margin-bottom: 16px; }
.no-session h2 { font-size: 18px; font-weight: 700; color: rgba(255,255,255,.6); margin-bottom: 8px; }
.no-session p  { font-size: 13px; color: rgba(255,255,255,.3); line-height: 1.6; }

/* spinner */
.spinner {
    width: 20px; height: 20px;
    border: 2px solid rgba(255,255,255,.2);
    border-top-color: #f0a500;
    border-radius: 50%;
    animation: spin .7s linear infinite;
    margin: 8px auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="phone">

    {{-- Persistent header --}}
    <div class="header">
        <div class="church-tag">Chrisco Upper Room</div>
        @if($session)
        <div class="service-name">{{ $session->label_display }}</div>
        <div class="service-meta">
            {{ $session->service_date->format('d F Y') }}
            @if(request('door')) &nbsp;·&nbsp; Door {{ strtoupper(request('door')) }} @endif
        </div>
        @else
        <div class="service-name">Sunday Check-In</div>
        @endif
    </div>

    @if(!$session)
    {{-- No active session --}}
    <div class="no-session">
        <i class="fas fa-clock"></i>
        <h2>No Service Open</h2>
        <p>Check-in is not available right now.<br>Please come back during the Sunday service.</p>
    </div>

    @else
    {{-- Screen 1: Search --}}
    <div class="screen active" id="s1">
        <p class="prompt">Type your name to check&nbsp;in</p>
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input id="search-input" type="text" autocomplete="off" placeholder="Search your name…" autofocus>
        </div>
        <p class="hint">Type at least 3 letters to search<br><br>Already checked in?<br>You'll see a confirmation.</p>
    </div>

    {{-- Screen 2: Results --}}
    <div class="screen" id="s2">
        <p class="sub-prompt">Tap your name below</p>
        <input class="search-input-sm" id="search-display" type="text" readonly>
        <div class="result-list" id="result-list"></div>
        <p class="bottom-hint" id="no-result-hint" style="display:none;">Not seeing your name?<br>Try a different spelling</p>
    </div>

    {{-- Screen 3: Confirm --}}
    <div class="screen" id="s3" style="align-items:center;justify-content:center;">
        <div class="confirm-avatar" id="c-avatar"></div>
        <div class="confirm-name"  id="c-name"></div>
        <div class="confirm-dept"  id="c-dept"></div>
        <div class="confirm-question">Is this you?</div>
        <button class="btn-yes" id="btn-yes">
            <i class="fas fa-check"></i> Yes, check me in
        </button>
        <button class="btn-back" onclick="goBack()">← Not me, go back</button>
        <div id="checkin-error" style="display:none;margin-top:10px;font-size:13px;color:#f87171;text-align:center;"></div>
    </div>

    {{-- Screen 4: Success --}}
    <div class="screen" id="s4" style="align-items:center;justify-content:center;">
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <div class="success-heading">Checked in!</div>
        <div class="success-name" id="s-name"></div>
        <div class="success-meta" id="s-meta"></div>
        <div class="scripture">
            "I was glad when they said unto me, Let us go into the house of the&nbsp;LORD."
            <cite>— Psalm 122:1</cite>
        </div>
        <button class="btn-again" onclick="reset()">Check in someone else</button>
    </div>

    {{-- Screen: Already checked in --}}
    <div class="screen" id="s-already" style="align-items:center;justify-content:center;">
        <div class="already-icon"><i class="fas fa-clock-rotate-left"></i></div>
        <div class="success-heading" style="color:#f0a500;">Already Checked In</div>
        <div class="success-name" id="already-name" style="font-size:18px;color:#fff;margin-top:4px;"></div>
        <div class="success-meta" id="already-meta"></div>
        <button class="btn-again" onclick="reset()">← Back to search</button>
    </div>
    @endif

</div>

@if($session)
<script>
const CSRF       = '{{ csrf_token() }}';
const DOOR       = '{{ request("door", "A") }}';
const SESSION_ID = {{ $session->id }};

let selectedMember = null;
let searchTimer    = null;
let membersCache   = {};

const s1 = document.getElementById('s1');
const s2 = document.getElementById('s2');
const s3 = document.getElementById('s3');
const s4 = document.getElementById('s4');
const sA = document.getElementById('s-already');

function showScreen(el) {
    [s1,s2,s3,s4,sA].forEach(s => s.classList.remove('active'));
    el.classList.add('active');
}

// Search on screen 1
document.getElementById('search-input').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 3) return;
    searchTimer = setTimeout(() => fetchResults(q), 350);
});

async function fetchResults(q) {
    document.getElementById('search-display').value = q;
    document.getElementById('result-list').innerHTML = '<div class="spinner"></div>';
    document.getElementById('no-result-hint').style.display = 'none';
    showScreen(s2);

    try {
        const r = await fetch('/attend/search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ q }),
        });
        const d = await r.json();

        if (!d.members || !d.members.length) {
            document.getElementById('result-list').innerHTML = '';
            document.getElementById('no-result-hint').style.display = 'block';
            return;
        }

        membersCache = {};
        document.getElementById('result-list').innerHTML = d.members.map(m => {
            membersCache[m.id] = m;
            const ini = initials(m.name);
            const avatar = m.photo
                ? `<div class="avatar"><img src="${m.photo}"></div>`
                : `<div class="avatar ${m.already_in ? 'green' : ''}">${ini}</div>`;
            return `<div class="result-item ${m.already_in ? 'selected' : ''}" onclick="selectMember(${m.id})">
                ${avatar}
                <div>
                    <div class="item-name">${m.name}</div>
                    <div class="item-sub">${m.department || ''}</div>
                </div>
                ${m.already_in ? '<i class="fas fa-check" style="margin-left:auto;color:#4ade80;font-size:14px;"></i>' : ''}
            </div>`;
        }).join('');
    } catch(e) {
        document.getElementById('result-list').innerHTML = '<p style="color:rgba(255,255,255,.3);text-align:center;font-size:13px;padding:12px 0;">Error loading results.</p>';
    }
}

function selectMember(id) {
    const m = membersCache[id];
    selectedMember = m;

    if (m.already_in) {
        document.getElementById('already-name').textContent = m.name;
        document.getElementById('already-meta').textContent = (m.checked_in_at || '') + (m.checked_in_at ? ' · Door ' + DOOR.toUpperCase() : '');
        showScreen(sA);
        return;
    }

    const ini = initials(m.name);
    const avEl = document.getElementById('c-avatar');
    avEl.innerHTML = m.photo ? `<img src="${m.photo}">` : ini;
    document.getElementById('c-name').textContent = m.name;
    document.getElementById('c-dept').textContent = m.department || '';
    document.getElementById('checkin-error').style.display = 'none';
    showScreen(s3);
}

document.getElementById('btn-yes').addEventListener('click', async function() {
    if (!selectedMember) return;
    this.disabled = true;
    this.innerHTML = '<div class="spinner" style="width:16px;height:16px;margin:0;"></div>';

    try {
        const r = await fetch('/attend/checkin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ user_id: selectedMember.id, door: DOOR }),
        });
        const d = await r.json();

        if (d.error === 'already_checked_in') {
            document.getElementById('already-name').textContent = selectedMember.name;
            document.getElementById('already-meta').textContent = '';
            showScreen(sA);
        } else if (r.ok) {
            document.getElementById('s-name').textContent = selectedMember.name;
            document.getElementById('s-meta').textContent =
                new Date().toLocaleTimeString('en-KE', {hour:'2-digit',minute:'2-digit'}) + ' · Door ' + DOOR.toUpperCase();
            showScreen(s4);
        } else {
            const errEl = document.getElementById('checkin-error');
            errEl.textContent = d.error || 'Something went wrong. Try again.';
            errEl.style.display = 'block';
        }
    } catch(e) {
        const errEl = document.getElementById('checkin-error');
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
    }

    this.disabled = false;
    this.innerHTML = '<i class="fas fa-check"></i> Yes, check me in';
});

function goBack() {
    showScreen(s2);
}

function reset() {
    selectedMember = null;
    document.getElementById('search-input').value = '';
    document.getElementById('result-list').innerHTML = '';
    document.getElementById('no-result-hint').style.display = 'none';
    showScreen(s1);
    setTimeout(() => document.getElementById('search-input').focus(), 100);
}

function initials(name) {
    const parts = name.trim().split(/\s+/).filter(Boolean);
    return ((parts[0]?.[0] || '') + (parts[parts.length-1]?.[0] || '')).toUpperCase();
}
</script>
@endif
</body>
</html>
