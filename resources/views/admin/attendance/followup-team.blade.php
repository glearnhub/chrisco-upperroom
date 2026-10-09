@extends('layouts.admin')

@section('title', 'Follow-Up Team — ' . $monthName)
@section('page-title', 'Follow-Up Team')

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Follow-Up Team</h1>
        <p class="text-gray-500 text-sm mt-1">Members who need follow-up — {{ $monthName }}</p>
    </div>
    {{-- Month selector --}}
    <form method="GET" action="{{ route('admin.attendance.followup.team') }}" class="flex items-center gap-2">
        <select name="month" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</option>
            @endforeach
        </select>
        <select name="year" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            @foreach(range(now()->year - 1, now()->year + 1) as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#0a1f44;">
            <i class="fas fa-search mr-1"></i> Load
        </button>
    </form>
</div>

{{-- Summary chips --}}
<div class="flex flex-wrap gap-3 mb-6">
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100">
        <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
        <span class="font-bold text-gray-800">{{ count($inactiveMembers) }}</span>
        <span class="text-sm text-gray-500">Inactive</span>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100">
        <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
        <span class="font-bold text-gray-800">{{ count($irregularMembers) }}</span>
        <span class="text-sm text-gray-500">Irregular / At Risk</span>
    </div>
    @php
        $followedUp = $followups->count();
        $total = count($inactiveMembers) + count($irregularMembers);
    @endphp
    <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-sm border border-gray-100">
        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
        <span class="font-bold text-gray-800">{{ $followedUp }} / {{ $total }}</span>
        <span class="text-sm text-gray-500">Followed up</span>
    </div>
</div>

{{-- ── INACTIVE MEMBERS ── --}}
<div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden mb-6" style="border-color:#dc2626;">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
        <h2 class="font-bold" style="color:#dc2626;">
            <i class="fas fa-exclamation-circle mr-2"></i>Inactive Members
            <span class="ml-2 text-sm font-semibold text-gray-500">(missed 3+ Sundays)</span>
        </h2>
        <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">{{ count($inactiveMembers) }} members</span>
    </div>
    @if(count($inactiveMembers))
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-red-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide hidden lg:table-cell">Department</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wide">Attended</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-red-700 uppercase tracking-wide">Missed</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-red-700 uppercase tracking-wide">Follow-Up</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @php $fuDataPrinted = []; @endphp
            @foreach($inactiveMembers as $i => $m)
            @php $fu = $followups[$m->id] ?? null; @endphp
            <tr class="hover:bg-red-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-800">{{ $m->full_name }}</div>
                    @if($m->office)<div class="text-xs text-gray-400">{{ $m->office }}</div>@endif
                </td>
                <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->phone ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">{{ $m->department ?: '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                        {{ $m->sundays_attended }} / {{ $totalSundays }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-200 text-red-800">{{ $m->sundays_missed }}</span>
                </td>
                <td class="px-4 py-3" style="min-width:160px;">
                    <div id="fu-badge-{{ $m->id }}" class="{{ $fu ? '' : 'hidden' }}">
                        <span id="fu-label-{{ $m->id }}"
                              class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 cursor-pointer"
                              onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')">
                            {{ $fu ? $fu->reason_label : '' }}
                        </span>
                        @if($fu && $fu->transferred_to)
                        <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5">→ {{ $fu->transferred_to }}</div>
                        @else
                        <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5 hidden"></div>
                        @endif
                    </div>
                    <button id="fu-btn-{{ $m->id }}"
                            onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')"
                            class="text-xs px-2 py-1 rounded border font-semibold {{ $fu ? 'hidden' : '' }}"
                            style="border-color:#dc2626;color:#dc2626;">
                        <i class="fas fa-plus mr-1"></i>Record Reason
                    </button>
                </td>
            </tr>
            @php $fuDataPrinted[$m->id] = true; @endphp
            <script>
            window._fuData = window._fuData || {};
            window._fuData[{{ $m->id }}] = {
                reason: '{{ $fu?->reason ?? '' }}',
                transferred_to: '{{ addslashes($fu?->transferred_to ?? '') }}',
                notes: '{{ addslashes($fu?->notes ?? '') }}',
            };
            </script>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
    <div class="px-5 py-8 text-center text-gray-400 text-sm">
        <i class="fas fa-check-double mr-2 text-green-400"></i>No inactive members this month.
    </div>
    @endif
</div>

{{-- ── IRREGULAR MEMBERS ── --}}
@if(count($irregularMembers))
<div class="bg-white rounded-xl shadow-sm border-l-4 overflow-hidden mb-6" style="border-color:#d97706;">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
        <h2 class="font-bold" style="color:#d97706;">
            <i class="fas fa-exclamation-triangle mr-2"></i>Irregular / At Risk
            <span class="ml-2 text-sm font-semibold text-gray-500">(attended 1–2, missed 1–2)</span>
        </h2>
        <span class="px-3 py-1 rounded-full text-sm font-bold bg-yellow-100 text-yellow-700">{{ count($irregularMembers) }} members</span>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-yellow-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide">#</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide hidden sm:table-cell">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide hidden lg:table-cell">Department</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wide">Attended</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-yellow-700 uppercase tracking-wide">Missed</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-yellow-700 uppercase tracking-wide">Follow-Up</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($irregularMembers as $i => $m)
            @php $fu = $followups[$m->id] ?? null; @endphp
            <tr class="hover:bg-yellow-50">
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                <td class="px-4 py-3">
                    <div class="font-semibold text-gray-800">{{ $m->full_name }}</div>
                    @if($m->office)<div class="text-xs text-gray-400">{{ $m->office }}</div>@endif
                </td>
                <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $m->phone ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">{{ $m->department ?: '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                        {{ $m->sundays_attended }} / {{ $totalSundays }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-xs text-gray-500">{{ $m->sundays_missed }}</td>
                <td class="px-4 py-3" style="min-width:160px;">
                    <div id="fu-badge-{{ $m->id }}" class="{{ $fu ? '' : 'hidden' }}">
                        <span id="fu-label-{{ $m->id }}"
                              class="inline-block px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-700 cursor-pointer"
                              onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')">
                            {{ $fu ? $fu->reason_label : '' }}
                        </span>
                        @if($fu && $fu->transferred_to)
                        <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5">→ {{ $fu->transferred_to }}</div>
                        @else
                        <div id="fu-church-{{ $m->id }}" class="text-xs text-gray-500 mt-0.5 hidden"></div>
                        @endif
                    </div>
                    <button id="fu-btn-{{ $m->id }}"
                            onclick="openFollowupModal({{ $m->id }}, '{{ addslashes($m->full_name) }}')"
                            class="text-xs px-2 py-1 rounded border font-semibold {{ $fu ? 'hidden' : '' }}"
                            style="border-color:#d97706;color:#d97706;">
                        <i class="fas fa-plus mr-1"></i>Record Reason
                    </button>
                </td>
            </tr>
            @if(!isset($fuDataPrinted[$m->id]))
            <script>
            window._fuData = window._fuData || {};
            window._fuData[{{ $m->id }}] = {
                reason: '{{ $fu?->reason ?? '' }}',
                transferred_to: '{{ addslashes($fu?->transferred_to ?? '') }}',
                notes: '{{ addslashes($fu?->notes ?? '') }}',
            };
            </script>
            @php $fuDataPrinted[$m->id] = true; @endphp
            @endif
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@elseif(!count($inactiveMembers))
<div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-400">
    <i class="fas fa-check-double text-3xl text-green-400 mb-3 block"></i>
    No members need follow-up for {{ $monthName }}.
</div>
@endif

{{-- ── FOLLOW-UP MODAL ── --}}
<div id="fu-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background:rgba(0,0,0,.45);">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-base" style="color:#0a1f44;">
                    <i class="fas fa-clipboard-list mr-2"></i>Follow-Up Reason
                </h3>
                <button onclick="closeFollowupModal()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-gray-500 mb-4">Recording reason for: <strong id="fu-modal-name" class="text-gray-800"></strong></p>
                <input type="hidden" id="fu-modal-uid">

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Reason for Missing</label>
                    <select id="fu-modal-reason"
                            onchange="document.getElementById('fu-church-wrap').style.display = this.value === 'transferred' ? 'block' : 'none'"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">— Select reason —</option>
                        @foreach($reasonLabels as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="fu-church-wrap" class="mb-4 hidden">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Church Transferred To</label>
                    <input type="text" id="fu-modal-church" placeholder="e.g. Chrisco Upper Room Westlands"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea id="fu-modal-notes" rows="2" placeholder="Any additional details…"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500 resize-none"></textarea>
                </div>

                <div id="fu-modal-err" class="hidden mb-3 text-sm text-red-600 font-semibold"></div>
            </div>
            <div class="px-5 pb-4 flex items-center gap-3">
                <button onclick="saveFollowup()"
                        class="flex-1 py-2 rounded-lg text-sm font-bold text-white"
                        style="background:#0a1f44;">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
                <button id="fu-modal-clear-btn" onclick="clearFollowup()"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50 hidden">
                    <i class="fas fa-trash mr-1"></i> Clear
                </button>
                <button onclick="closeFollowupModal()"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-50">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const FU_SAVE_URL   = '{{ route('admin.attendance.followup.save') }}';
const FU_DELETE_URL = '{{ route('admin.attendance.followup.delete') }}';
const CSRF          = document.querySelector('meta[name="csrf-token"]').content;
const REPORT_YEAR   = {{ $year }};
const REPORT_MONTH  = {{ $month }};
let _fuModalUid = null;

function openFollowupModal(uid, name) {
    _fuModalUid = uid;
    document.getElementById('fu-modal-name').textContent = name;
    document.getElementById('fu-modal-uid').value = uid;
    document.getElementById('fu-modal-err').classList.add('hidden');
    const existing = (window._fuData || {})[uid] || {};
    const reason = existing.reason || '';
    document.getElementById('fu-modal-reason').value = reason;
    document.getElementById('fu-modal-church').value = existing.transferred_to || '';
    document.getElementById('fu-modal-notes').value  = existing.notes || '';
    document.getElementById('fu-church-wrap').style.display = reason === 'transferred' ? 'block' : 'none';
    document.getElementById('fu-modal-clear-btn').classList.toggle('hidden', !reason);
    document.body.style.overflow = 'hidden';
    document.getElementById('fu-modal').classList.remove('hidden');
}

function closeFollowupModal() {
    document.getElementById('fu-modal').classList.add('hidden');
    document.body.style.overflow = '';
    _fuModalUid = null;
}

async function saveFollowup() {
    const uid    = _fuModalUid;
    const reason = document.getElementById('fu-modal-reason').value;
    const church = document.getElementById('fu-modal-church').value.trim();
    const notes  = document.getElementById('fu-modal-notes').value.trim();
    const errEl  = document.getElementById('fu-modal-err');
    if (!reason) { errEl.textContent = 'Please select a reason.'; errEl.classList.remove('hidden'); return; }
    if (reason === 'transferred' && !church) { errEl.textContent = 'Please enter the church name.'; errEl.classList.remove('hidden'); return; }
    errEl.classList.add('hidden');
    try {
        const res = await fetch(FU_SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: uid, year: REPORT_YEAR, month: REPORT_MONTH, reason, transferred_to: church, notes }),
        });
        const d = await res.json();
        if (!d.success) throw new Error();
        window._fuData = window._fuData || {};
        window._fuData[uid] = { reason, transferred_to: church, notes };
        document.getElementById('fu-label-' + uid).textContent = d.reason_label;
        const churchEl = document.getElementById('fu-church-' + uid);
        if (d.transferred_to) { churchEl.textContent = '→ ' + d.transferred_to; churchEl.classList.remove('hidden'); }
        else { churchEl.textContent = ''; churchEl.classList.add('hidden'); }
        document.getElementById('fu-badge-' + uid).classList.remove('hidden');
        document.getElementById('fu-btn-' + uid).classList.add('hidden');
        closeFollowupModal();
    } catch(e) {
        errEl.textContent = 'Failed to save. Please try again.';
        errEl.classList.remove('hidden');
    }
}

async function clearFollowup() {
    const uid = _fuModalUid;
    try {
        await fetch(FU_DELETE_URL, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ user_id: uid, year: REPORT_YEAR, month: REPORT_MONTH }),
        });
        window._fuData = window._fuData || {};
        window._fuData[uid] = { reason: '', transferred_to: '', notes: '' };
        document.getElementById('fu-badge-' + uid).classList.add('hidden');
        document.getElementById('fu-btn-' + uid).classList.remove('hidden');
        closeFollowupModal();
    } catch(e) {}
}

document.getElementById('fu-modal').addEventListener('click', function(e) {
    if (e.target === this) closeFollowupModal();
});
</script>
@endpush
