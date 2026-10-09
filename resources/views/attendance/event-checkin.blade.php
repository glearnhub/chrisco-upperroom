<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} — Check In</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('fa/css/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4fa; }
        .btn-navy  { background: #0a1f44; color: #f0a500; }
        .btn-navy:hover { background: #0d2856; }
        .btn-navy:disabled { background: #94a3b8; color: white; cursor: not-allowed; }
        .card { background: white; border-radius: 16px; box-shadow: 0 2px 16px rgba(10,31,68,0.08); }
        #search-input:focus { outline:none; border-color:#0a1f44; box-shadow:0 0 0 3px rgba(10,31,68,.1); }
        .result-row { border:2px solid #e2e8f0; border-radius:12px; padding:12px 14px; transition:border-color .15s; }
        .result-row.registered:not(.attended) { cursor:pointer; }
        .result-row.registered:not(.attended):hover { border-color:#0a1f44; background:#f8fafc; }
        .result-row.attended { border-color:#bbf7d0; background:#f0fdf4; }
        .result-row.unregistered { border-color:#fde68a; background:#fffbeb; cursor:pointer; }
        .result-row.unregistered:hover { border-color:#f59e0b; }
        .screen { display:none; }
        .screen.active { display:block; }
        @keyframes pop-in { from { transform:scale(.85);opacity:0; } to { transform:scale(1);opacity:1; } }
        .pop-in { animation:pop-in .3s ease; }
    </style>
</head>
<body class="min-h-screen">
<div class="max-w-md mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-5">
        <img src="{{ asset('images/logo.png') }}" alt="Chrisco Upper Room" class="h-10 w-auto">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400">Event Check-In</p>
            <h1 class="text-base font-extrabold leading-tight" style="color:#0a1f44;">{{ Str::limit($event->title, 40) }}</h1>
        </div>
    </div>

    {{-- Event card --}}
    <div class="card px-5 py-4 mb-4 flex items-center gap-4">
        @if($event->image)
        <img src="{{ asset('storage/'.$event->image) }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
        @else
        <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#0a1f44;">
            <i class="fas fa-calendar-alt text-xl" style="color:#f0a500;"></i>
        </div>
        @endif
        <div class="min-w-0">
            <p class="font-bold text-sm truncate" style="color:#0a1f44;">{{ $event->title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">
                <i class="fas fa-clock mr-1"></i>{{ $event->start_datetime->format('D, d M Y · g:i A') }}
            </p>
            @if($event->location)
            <p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i>{{ $event->location }}</p>
            @endif
        </div>
    </div>

    @if($status === 'not_yet')
    <div class="card p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-hourglass-start text-2xl text-blue-500"></i>
        </div>
        <h2 class="text-lg font-bold text-gray-800 mb-2">Check-In Not Open Yet</h2>
        <p class="text-gray-500 text-sm">Check-in opens 2 hours before the event starts.<br>
            <strong>{{ $event->start_datetime->subHours(2)->format('g:i A') }}</strong> on {{ $event->start_datetime->format('d M Y') }}
        </p>
    </div>

    @elseif($status === 'closed')
    <div class="card p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-2xl text-gray-400"></i>
        </div>
        <h2 class="text-lg font-bold text-gray-800 mb-2">Check-In Closed</h2>
        <p class="text-gray-500 text-sm">This event has ended. Check-in is no longer available.</p>
    </div>

    @else
    {{-- ── OPEN: search screens ── --}}

    {{-- Screen: Search --}}
    <div id="screen-search" class="screen active">
        <div class="card p-5">
            <p class="text-sm font-semibold mb-3" style="color:#0a1f44;">Search your name or phone number</p>
            <div class="relative mb-4">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input id="search-input" type="text" autocomplete="off" autocorrect="off"
                       placeholder="Type name or phone…"
                       class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-gray-200 text-base bg-gray-50">
            </div>
            <div id="search-results" class="flex flex-col gap-2 min-h-[40px]">
                <p class="text-sm text-gray-400 text-center py-3">Start typing to find yourself…</p>
            </div>
        </div>
    </div>

    {{-- Screen: Walk-in register form --}}
    <div id="screen-walkin" class="screen">
        <div class="card p-5">
            <button onclick="showScreen('search')" class="text-sm text-gray-400 hover:text-gray-600 mb-4 flex items-center gap-1">
                <i class="fas fa-arrow-left text-xs"></i> Back to search
            </button>
            <h2 class="text-base font-bold mb-1" style="color:#0a1f44;">Register at the Door</h2>
            <p class="text-sm text-gray-500 mb-4">You're not registered for this event. Fill in your details to register and check in now.</p>
            <input type="hidden" id="walkin-member-id" value="">
            <div class="flex flex-col gap-3 mb-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Full Name *</label>
                    <input id="walkin-name" type="text" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Phone Number *</label>
                    <input id="walkin-phone" type="tel" class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>
            <div id="walkin-error" class="hidden mb-3 px-3 py-2 rounded-lg text-sm" style="background:#fee2e2;color:#dc2626;"></div>
            <button id="walkin-btn" onclick="submitWalkin()"
                    class="w-full py-3 rounded-xl btn-navy font-semibold text-sm">
                <i class="fas fa-user-plus mr-1"></i> Register &amp; Check In
            </button>
        </div>
    </div>

    {{-- Screen: Confirm check-in --}}
    <div id="screen-confirm" class="screen">
        <div class="card p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3" id="confirm-avatar">
                <i class="fas fa-user text-2xl text-gray-300"></i>
            </div>
            <h2 id="confirm-name" class="text-xl font-bold mb-1" style="color:#0a1f44;"></h2>
            <p id="confirm-phone" class="text-sm text-gray-400 mb-1"></p>
            <p id="confirm-category" class="text-xs font-semibold uppercase tracking-wide mb-5 text-gray-400"></p>
            <p class="text-sm text-gray-500 mb-5">Is this you? Tap <strong>Yes, Check Me In</strong> to confirm.</p>
            <div class="flex gap-3">
                <button onclick="showScreen('search')" class="flex-1 py-3 rounded-xl border-2 border-gray-200 text-gray-600 font-semibold text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </button>
                <button id="confirm-btn" onclick="confirmCheckin()"
                        class="flex-1 py-3 rounded-xl btn-navy font-semibold text-sm">
                    <i class="fas fa-check mr-1"></i> Yes, Check Me In
                </button>
            </div>
        </div>
    </div>

    {{-- Screen: Success --}}
    <div id="screen-success" class="screen">
        <div class="card p-8 text-center pop-in">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#dcfce7;">
                <i class="fas fa-check-circle text-4xl" style="color:#16a34a;"></i>
            </div>
            <h2 class="text-xl font-bold mb-1" style="color:#0a1f44;">You're Checked In!</h2>
            <p id="success-name" class="text-lg font-semibold text-gray-700 mb-1"></p>
            <p class="text-sm text-gray-400 mb-2">Welcome to <strong>{{ $event->title }}</strong></p>
            <p class="text-xs text-gray-400">Enjoy the event 🙏</p>
        </div>
    </div>

    {{-- Screen: Already checked in --}}
    <div id="screen-already" class="screen">
        <div class="card p-8 text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background:#fef9c3;">
                <i class="fas fa-circle-check text-3xl" style="color:#ca8a04;"></i>
            </div>
            <h2 class="text-lg font-bold mb-1" style="color:#0a1f44;">Already Checked In</h2>
            <p id="already-name" class="font-semibold text-gray-700 mb-2"></p>
            <p class="text-sm text-gray-400 mb-4">You're already registered as attending this event.</p>
            <button onclick="resetAll()" class="text-sm text-blue-600 underline">Back to search</button>
        </div>
    </div>

    @endif

</div>

<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const BASE_URL = '/attend/event/{{ $event->id }}';
let selectedRegId = null;
let searchTimer = null;

const searchInput   = document.getElementById('search-input');
const searchResults = document.getElementById('search-results');

if (searchInput) {
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        const q = searchInput.value.trim();
        if (q.length < 2) {
            searchResults.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Start typing to find yourself…</p>';
            return;
        }
        searchResults.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Searching…</p>';
        searchTimer = setTimeout(() => doSearch(q), 350);
    });
}

async function doSearch(q) {
    try {
        const r = await fetch(BASE_URL + '/search', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ q }),
        });
        const data = await r.json();
        if (!r.ok) { searchResults.innerHTML = `<p class="text-sm text-red-500 text-center py-3">${data.error || 'Search failed.'}</p>`; return; }

        let html = '';

        // Registered people
        data.registered.forEach(reg => {
            html += `<div class="result-row registered ${reg.attended ? 'attended' : ''}" onclick="${reg.attended ? '' : `selectReg(${reg.id}, '${escJ(reg.full_name)}', '${escJ(reg.phone||'')}', '${escJ(reg.category||'')}'); event.stopPropagation();`}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center" style="background:#e0e7ef;">
                        <i class="fas fa-user text-gray-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate" style="color:#0a1f44;">${reg.full_name}</p>
                        <p class="text-xs text-gray-400">${reg.phone || ''} ${reg.category ? '· ' + ucFirst(reg.category) : ''}</p>
                    </div>
                    ${reg.attended
                        ? `<span class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check mr-1"></i>Checked in</span>`
                        : `<span class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0 cursor-pointer" style="background:#0a1f44;color:#f0a500;">Check In <i class="fas fa-chevron-right ml-1 text-xs"></i></span>`
                    }
                </div>
            </div>`;
        });

        // Unregistered members (walk-in suggestions)
        if (data.unregistered.length) {
            html += `<div class="mt-2 mb-1"><p class="text-xs font-semibold uppercase tracking-wide text-amber-600"><i class="fas fa-info-circle mr-1"></i>Not yet registered for this event:</p></div>`;
            data.unregistered.forEach(m => {
                html += `<div class="result-row unregistered" onclick="openWalkin('${escJ(m.name)}', '${escJ(m.phone||'')}', ${m.id}); event.stopPropagation();">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center bg-amber-50">
                            <i class="fas fa-user-plus text-amber-400 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold truncate" style="color:#92400e;">${m.name}</p>
                            <p class="text-xs text-gray-400">${m.phone || ''}</p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0" style="background:#fef3c7;color:#92400e;">Register &amp; In</span>
                    </div>
                </div>`;
            });
        }

        if (!html) {
            html = `<div class="text-center py-4">
                <p class="text-sm text-gray-500 mb-3">No one found with that name or phone.</p>
                <button onclick="openWalkin('', '', null)" class="text-sm font-semibold px-4 py-2 rounded-xl" style="background:#fef3c7;color:#92400e;">
                    <i class="fas fa-user-plus mr-1"></i> Register at the door
                </button>
            </div>`;
        }

        searchResults.innerHTML = html;
    } catch(e) {
        searchResults.innerHTML = '<p class="text-sm text-red-500 text-center py-3">An error occurred.</p>';
    }
}

function selectReg(id, name, phone, category) {
    selectedRegId = id;
    document.getElementById('confirm-name').textContent     = name;
    document.getElementById('confirm-phone').textContent    = phone;
    document.getElementById('confirm-category').textContent = category ? ucFirst(category) : '';
    showScreen('confirm');
}

async function confirmCheckin() {
    const btn = document.getElementById('confirm-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Checking in…';
    try {
        const r = await fetch(BASE_URL + '/checkin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ registration_id: selectedRegId }),
        });
        const data = await r.json();
        if (r.status === 409) {
            document.getElementById('already-name').textContent = data.name;
            showScreen('already'); return;
        }
        if (!r.ok) { alert(data.error || 'Check-in failed.'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-check mr-1"></i> Yes, Check Me In'; return; }
        document.getElementById('success-name').textContent = data.name;
        showScreen('success');
    } catch(e) {
        alert('Network error. Try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Yes, Check Me In';
    }
}

function openWalkin(name, phone, memberId) {
    document.getElementById('walkin-name').value    = name;
    document.getElementById('walkin-phone').value   = phone;
    document.getElementById('walkin-member-id').value = memberId || '';
    document.getElementById('walkin-error').classList.add('hidden');
    showScreen('walkin');
}

async function submitWalkin() {
    const name     = document.getElementById('walkin-name').value.trim();
    const phone    = document.getElementById('walkin-phone').value.trim();
    const memberId = document.getElementById('walkin-member-id').value;
    const errDiv   = document.getElementById('walkin-error');
    const btn      = document.getElementById('walkin-btn');

    if (!name || !phone) { showError(errDiv, 'Name and phone number are required.'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Registering…';
    errDiv.classList.add('hidden');

    try {
        const r = await fetch(BASE_URL + '/walkin', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ full_name: name, phone, member_id: memberId || null }),
        });
        const data = await r.json();
        if (!r.ok) { showError(errDiv, data.message || data.error || 'Registration failed.'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus mr-1"></i> Register & Check In'; return; }
        document.getElementById('success-name').textContent = data.name;
        showScreen('success');
    } catch(e) {
        showError(errDiv, 'Network error. Try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-user-plus mr-1"></i> Register & Check In';
    }
}

function resetAll() {
    selectedRegId = null;
    searchInput.value = '';
    searchResults.innerHTML = '<p class="text-sm text-gray-400 text-center py-3">Start typing to find yourself…</p>';
    showScreen('search');
}

function showScreen(name) {
    document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
    document.getElementById('screen-' + name).classList.add('active');
}
function escJ(s) { return (s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"'); }
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }
function showError(el, msg) { el.textContent = msg; el.classList.remove('hidden'); }
</script>
</body>
</html>
