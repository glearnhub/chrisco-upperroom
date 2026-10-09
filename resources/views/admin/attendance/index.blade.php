@extends('layouts.admin')

@section('title', 'Attendance')
@section('page-title', 'Sunday Attendance')

@section('content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold" style="color:#0a1f44;">Sunday Attendance</h1>
        <p class="text-gray-500 text-sm mt-1">Open a session to allow members to check in via QR code</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.attendance.report') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#0a1f44;">
            <i class="fas fa-chart-bar"></i> Monthly Report
        </a>
        <a href="{{ route('admin.attendance.qr-codes') }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
            <i class="fas fa-qrcode"></i> View QR Codes
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#dcfce7;color:#15803d;">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium" style="background:#fee2e2;color:#dc2626;">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

@if($openSession)
{{-- ── OPEN SESSION ── --}}
<div class="bg-white rounded-xl shadow-sm border-2 border-green-300 p-6 mb-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold mb-2" style="background:#dcfce7;color:#15803d;">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse inline-block"></span> LIVE
            </span>
            <h2 class="text-lg font-bold" style="color:#0a1f44;">{{ $openSession->label_display }}</h2>
            <p class="text-sm text-gray-500">
                {{ $openSession->service_date->format('l, d M Y') }}
                &nbsp;·&nbsp; Opened {{ $openSession->opened_at->format('H:i') }}
                @if($openSession->openedBy) by {{ $openSession->openedBy->full_name }} @endif
            </p>
        </div>
        <div class="text-center px-6 py-3 rounded-xl" style="background:#0a1f44;">
            <p class="text-4xl font-extrabold" style="color:#f0a500;" id="live-count">{{ $openSession->attendanceCount() }}</p>
            <p class="text-xs" style="color:rgba(255,255,255,0.6);">checked in</p>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-3">
        <a href="{{ route('admin.attendance.session', $openSession) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
           style="background:#0a1f44;">
            <i class="fas fa-user-check"></i> Usher Check-In Panel
        </a>
        @if(auth()->user()->hasPermission('attendance.close'))
        <form method="POST" action="{{ route('admin.attendance.close', $openSession) }}"
              data-confirm="Close this session? Members will no longer be able to check in." data-confirm-ok="Close Session" data-confirm-type="warning">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg border-2 text-sm font-semibold"
                    style="border-color:#dc2626;color:#dc2626;">
                <i class="fas fa-stop-circle"></i> Close Session
            </button>
        </form>
        @endif
    </div>
</div>
@else
{{-- ── OPEN A NEW SESSION ── --}}
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h2 class="text-base font-bold mb-4" style="color:#0a1f44;"><i class="fas fa-play-circle mr-2 text-green-600"></i>Open New Session</h2>
    <form method="POST" action="{{ route('admin.attendance.open') }}" class="flex flex-col sm:flex-row gap-3 flex-wrap">
        @csrf
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</label>
            <input type="date" name="service_date" value="{{ now()->format('Y-m-d') }}" required
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</label>
            <select name="service_type" class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <option value="sunday_morning">Sunday Morning</option>
                <option value="sunday_afternoon">Sunday Afternoon</option>
                <option value="special">Special Service</option>
            </select>
        </div>
        <div class="flex flex-col gap-1 flex-1">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Label (optional)</label>
            <input type="text" name="label" placeholder="e.g. Youth Sunday, 1st Service…"
                   class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
        </div>
        <div class="flex flex-col gap-1 justify-end">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide opacity-0">-</label>
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white"
                    style="background:#0a1f44;">
                <i class="fas fa-play"></i> Open Session
            </button>
        </div>
    </form>
</div>
@endif

{{-- ── RECENT SESSIONS ── --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="text-base font-bold" style="color:#0a1f44;">Recent Sessions</h2>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wide border-b border-gray-100">
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Service</th>
                <th class="px-5 py-3 text-center">Attended</th>
                <th class="px-5 py-3">Duration</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($recentSessions as $s)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-semibold" style="color:#0a1f44;">{{ $s->service_date->format('d M Y') }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $s->label_display }}</td>
                <td class="px-5 py-3 text-center font-bold" style="color:#0a1f44;">{{ $s->attendanceCount() }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs">
                    @if($s->opened_at && $s->closed_at)
                        {{ $s->opened_at->format('H:i') }} – {{ $s->closed_at->format('H:i') }}
                    @elseif($s->opened_at)
                        Since {{ $s->opened_at->format('H:i') }}
                    @else —
                    @endif
                </td>
                <td class="px-5 py-3">
                    @if($s->status === 'open')
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold" style="background:#dcfce7;color:#15803d;">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Open
                        </span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Closed</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.attendance.session', $s) }}"
                       class="text-xs font-semibold hover:underline" style="color:#0a1f44;">
                        View <i class="fas fa-chevron-right text-xs ml-1"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-8 text-center text-gray-400">No sessions yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection

@push('scripts')
@if($openSession)
<script>
// Poll every 15s to refresh the live count
setInterval(async () => {
    try {
        const r = await fetch('{{ route('admin.attendance.session', $openSession) }}/count');
        const d = await r.json();
        document.getElementById('live-count').textContent = d.count;
    } catch(e) {}
}, 15000);
</script>
@endif
@endpush
