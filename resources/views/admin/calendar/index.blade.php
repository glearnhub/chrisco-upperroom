@extends('layouts.admin')
@section('title', 'Church Calendar ' . $year)

@push('styles')
<style>
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); }
.cal-header-cell { background:#1e3a6e; color:#fff; text-align:center; padding:10px 4px; font-size:11px; font-weight:700; text-transform:uppercase; border-right:1px solid #2d5090; border-bottom:1px solid #2d5090; }
.cal-header-cell:last-child { border-right:none; }
.cal-cell { border-right:1px solid #c8d3e0; border-bottom:1px solid #c8d3e0; min-height:100px; padding:6px; background:#fffff0; position:relative; }
.cal-cell.other-month { background:#e5e8ee; }
.cal-cell.today { background:#fffde7; outline:2px solid #f0a500; outline-offset:-2px; }
.cal-day-num { font-size:14px; font-weight:700; color:#1e3a6e; line-height:1; }
.cal-event { font-size:10px; font-weight:600; line-height:1.3; padding:2px 5px; border-radius:3px; margin-top:3px; cursor:pointer; word-break:break-word; color:#fff !important; display:block; }
.cal-notes-cell { background:#d0d8e4; border-right:1px solid #c8d3e0; border-bottom:1px solid #c8d3e0; padding:8px; font-size:11px; color:#444; font-weight:600; }

@media print {
    .no-print { display:none !important; }
    body { background:#fff !important; }
    .cal-cell { min-height:80px; }
}
@media (max-width: 640px) {
    .cal-scroll-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .cal-grid { min-width: 560px; }
    .cal-cell { min-height: 70px; padding: 4px; }
    .cal-header-cell { padding: 6px 2px; font-size: 9px; }
    .cal-day-num { font-size: 12px; }
}
</style>
@endpush

@section('content')
<div class="p-3 sm:p-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5 no-print">
        <div>
            <h1 class="text-2xl font-bold" style="color:#1e3a6e;">
                <i class="fas fa-calendar-week mr-2" style="color:#f0a500;"></i>Church Calendar
            </h1>
            <p class="text-gray-500 text-sm mt-0.5">Ministry events calendar</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.calendar.import.form') }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-file-excel" style="color:#16a34a;"></i> <span class="hidden sm:inline">Import Excel</span>
            </a>
            <button onclick="openExportModal()"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                <i class="fas fa-image"></i> <span class="hidden sm:inline">Export Image</span>
            </button>
            <a href="{{ route('admin.calendar.print', ['year' => $year, 'month' => $month]) }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700">
                <i class="fas fa-print"></i> <span class="hidden sm:inline">Print Month</span>
            </a>
            <a href="{{ route('admin.calendar.print', ['year' => $year]) }}" target="_blank"
               class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold text-gray-700">
                <i class="fas fa-file-pdf"></i> <span class="hidden sm:inline">Print Year</span>
            </a>
            <a href="{{ route('admin.calendar.create') }}?year={{ $year }}&month={{ $month }}"
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#1e3a6e;">
                <i class="fas fa-plus"></i> Add Event
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded flex items-center justify-between no-print">
        <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
    @endif

    {{-- Month / Year navigation --}}
    @php
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear  = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear  = $month == 12 ? $year + 1 : $year;
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 mb-4 flex items-center gap-3 no-print flex-wrap">
        {{-- Prev month --}}
        <a href="{{ route('admin.calendar.index', ['year' => $prevYear, 'month' => $prevMonth]) }}"
           class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
            <i class="fas fa-chevron-left"></i>
        </a>

        {{-- Month picker --}}
        <form method="GET" action="{{ route('admin.calendar.index') }}" class="flex items-center gap-2">
            <select name="month" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 text-sm font-bold" style="color:#1e3a6e;">
                @foreach($months as $mn => $mname)
                <option value="{{ $mn }}" {{ $mn == $month ? 'selected' : '' }}>{{ $mname }}</option>
                @endforeach
            </select>
            <select name="year" onchange="this.form.submit()"
                    class="border border-gray-300 rounded px-2 py-1 text-sm font-bold" style="color:#1e3a6e;">
                @foreach(range(now()->year - 2, now()->year + 5) as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        {{-- Next month --}}
        <a href="{{ route('admin.calendar.index', ['year' => $nextYear, 'month' => $nextMonth]) }}"
           class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
            <i class="fas fa-chevron-right"></i>
        </a>

        <span class="text-sm text-gray-500 ml-2">{{ $monthEvents->count() }} event(s) this month</span>
    </div>

    {{-- Single month calendar --}}
    {{-- export-wrapper wraps title bar + grid for image capture --}}
    @php
        $firstDay    = \Carbon\Carbon::create($year, $month, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow    = $firstDay->dayOfWeek;
    @endphp
    <div id="calendar-export-wrapper" class="rounded-xl overflow-hidden shadow border border-gray-200" style="background:#fff;">
        {{-- Month title bar --}}
        <div class="flex items-center justify-between px-4 py-3 text-white font-bold text-lg" style="background:#1e3a6e;">
            <span>{{ $months[$month] }} {{ $year }}</span>
            <a href="{{ route('admin.calendar.create') }}?year={{ $year }}&month={{ $month }}"
               class="text-sm text-blue-200 hover:text-white no-print font-semibold">
                <i class="fas fa-plus mr-1"></i> Add
            </a>
        </div>

        {{-- Day headers + grid --}}
        <div class="cal-scroll-wrap">
        <div class="cal-grid">
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $dh)
            <div class="cal-header-cell">{{ $dh }}</div>
            @endforeach

            {{-- Empty cells before month start --}}
            @for($i = 0; $i < $startDow; $i++)
            <div class="cal-cell other-month"></div>
            @endfor

            {{-- Days --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
            @php
                $dateStr   = \Carbon\Carbon::create($year, $month, $d)->toDateString();
                $isToday   = $dateStr === now()->toDateString();
                $dayEvents = $eventsByDate[$dateStr] ?? [];
            @endphp
            <div class="cal-cell {{ $isToday ? 'today' : '' }}">
                <div class="cal-day-num">{{ $d }}</div>
                @foreach($dayEvents as $ev)
                <a href="{{ route('admin.calendar.edit', $ev) }}"
                   class="cal-event" style="background:{{ $ev->color }}; {{ !$ev->is_published ? 'opacity:0.55;' : '' }}"
                   title="{{ $ev->title }}{{ !$ev->is_published ? ' (Private)' : '' }}">
                    @if(!$ev->is_published)<i class="fas fa-lock" style="font-size:8px;margin-right:2px;opacity:0.8;"></i>@endif{{ Str::limit($ev->title, 26) }}
                </a>
                @endforeach
            </div>
            @endfor

            {{-- Trailing cells --}}
            @php
                $totalCells = $startDow + $daysInMonth;
                $remainder  = $totalCells % 7;
                $trailing   = $remainder > 0 ? 7 - $remainder : 0;
            @endphp
            @if($trailing > 0)
                @for($i = 0; $i < $trailing - 1; $i++)
                <div class="cal-cell other-month"></div>
                @endfor
                <div class="cal-notes-cell">Notes:</div>
            @endif
        </div>
        </div>{{-- /cal-scroll-wrap --}}
    </div>

    {{-- Event list for this month --}}
    @if($monthEvents->count())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-5 no-print">
        <div class="px-5 py-3 border-b border-gray-100">
            <h2 class="font-bold text-gray-800"><i class="fas fa-list mr-1"></i> Events — {{ $months[$month] }} {{ $year }}</h2>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead style="background:#1e3a6e;">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-white">Event</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-white">Date(s)</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-white hidden sm:table-cell">Category</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-white">Visibility</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-white">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($monthEvents as $ev)
                <tr class="hover:bg-gray-50 {{ !$ev->is_published ? 'opacity-60' : '' }}">
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold text-white" style="background:{{ $ev->color }};">
                            {{ $ev->title }}
                        </span>
                        @if(!$ev->is_published)
                        <span class="ml-1 text-xs text-gray-400 italic">private</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-gray-600 text-xs">
                        {{ $ev->start_date->format('d M Y') }}
                        @if($ev->end_date && !$ev->end_date->eq($ev->start_date))
                            – {{ $ev->end_date->format('d M Y') }}
                        @endif
                    </td>
                    <td class="px-4 py-2 text-gray-500 text-xs hidden sm:table-cell">{{ $ev->category }}</td>
                    <td class="px-4 py-2">
                        <form method="POST" action="{{ route('admin.calendar.toggle-visibility', $ev) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    title="{{ $ev->is_published ? 'Click to make Private' : 'Click to make Public' }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border transition
                                           {{ $ev->is_published
                                                ? 'bg-green-50 text-green-700 border-green-300 hover:bg-red-50 hover:text-red-600 hover:border-red-300'
                                                : 'bg-gray-100 text-gray-500 border-gray-300 hover:bg-green-50 hover:text-green-700 hover:border-green-300' }}">
                                <i class="fas {{ $ev->is_published ? 'fa-globe' : 'fa-lock' }}"></i>
                                {{ $ev->is_published ? 'Public' : 'Private' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('admin.calendar.edit', $ev) }}"
                               class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700 hover:bg-blue-200">
                                <i class="fas fa-edit"></i> <span class="hidden sm:inline">Edit</span>
                            </a>
                            <a href="{{ route('admin.calendar.publish-event.form', $ev) }}"
                               class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800 hover:bg-yellow-200"
                               title="Publish this calendar event as a public Event with a poster">
                                <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Make Event</span>
                            </a>
                            <form method="POST" action="{{ route('admin.calendar.destroy', $ev) }}"
                                  data-confirm="Delete this event?" data-confirm-ok="Delete">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 rounded text-xs bg-red-100 text-red-700 hover:bg-red-200">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>{{-- /overflow-x-auto --}}
    </div>
    @else
    <div class="mt-5 text-center py-10 text-gray-400 bg-white rounded-xl border border-gray-100">
        <i class="fas fa-calendar-times text-3xl mb-2 block"></i>
        No events for {{ $months[$month] }} {{ $year }}.
        <a href="{{ route('admin.calendar.create') }}?year={{ $year }}&month={{ $month }}" class="ml-1 underline" style="color:#1e3a6e;">Add one</a>
    </div>
    @endif

</div>

{{-- ===================== EXPORT POSTER MODAL ===================== --}}
<div id="export-modal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,0.75);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[95vh] overflow-y-auto">

            <div class="flex items-center justify-between px-6 py-4 border-b" style="background:#1e3a6e; border-radius:1rem 1rem 0 0;">
                <h2 class="text-white font-bold text-lg"><i class="fas fa-image mr-2" style="color:#f0a500;"></i>Export Calendar Poster</h2>
                <button onclick="document.getElementById('export-modal').classList.add('hidden')" class="text-white/70 hover:text-white text-2xl leading-none">&times;</button>
            </div>

            <div class="p-5 space-y-4">

                {{-- Row 1: colours + font --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Theme Colour</label>
                        <div class="flex flex-wrap gap-2" id="theme-swatches">
                            @foreach([
                                ['#5c2e00','Chocolate'],['#1e3a6e','Navy'],['#8b1a1a','Crimson'],
                                ['#1a5c2e','Forest'],['#4a1a6e','Purple'],['#0e5c6e','Teal'],
                                ['#b8860b','Gold'],['#1a1a1a','Charcoal'],
                            ] as [$hex,$name])
                            <button type="button" onclick="setPosterColor('{{ $hex }}')" data-hex="{{ $hex }}" title="{{ $name }}"
                                    class="swatch w-9 h-9 rounded-full border-4 border-white shadow-md hover:scale-110 transition"
                                    style="background:{{ $hex }};"></button>
                            @endforeach
                            <label title="Custom" class="w-9 h-9 rounded-full border-4 border-dashed border-gray-300 flex items-center justify-center cursor-pointer hover:scale-110 transition" style="overflow:hidden;">
                                <input type="color" id="custom-color-picker" value="#5c2e00" oninput="setPosterColor(this.value)" class="opacity-0 w-0 h-0 absolute">
                                <i class="fas fa-palette text-gray-400 text-xs"></i>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Font Style</label>
                        <select id="poster-font" onchange="renderPosterPreview()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                            <option value="Impact">Impact (Bold Condensed)</option>
                            <option value="Arial Black">Arial Black</option>
                            <option value="Georgia">Georgia (Elegant Serif)</option>
                            <option value="Verdana">Verdana (Clean Sans)</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Palatino Linotype">Palatino (Classic Serif)</option>
                        </select>
                    </div>
                </div>

                {{-- Bible verse --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-1">Theme Verse <span class="font-normal normal-case text-gray-400">(optional)</span></label>
                    <input type="text" id="poster-verse" placeholder="e.g. PURSUE…OVERTAKE…RECOVER… 1 SAMUEL 30:8"
                           oninput="renderPosterPreview()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-200">
                </div>

                {{-- Preview --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2">Preview</label>
                    <div class="border rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center" style="max-height:500px;">
                        <canvas id="poster-preview" style="max-width:100%; max-height:500px; display:block;"></canvas>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2">
                    <button onclick="downloadPoster()"
                            class="flex-1 py-2.5 rounded-lg font-semibold text-white text-sm" style="background:#1e3a6e;">
                        <i class="fas fa-download mr-1"></i> Download PNG
                    </button>
                    <button onclick="document.getElementById('export-modal').classList.add('hidden')"
                            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 text-sm font-semibold">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ---- Blade data ----
const POSTER_MONTH  = '{{ strtoupper($months[$month]) }}';
const POSTER_YEAR   = '{{ $year }}';
const POSTER_LOGO   = '{{ asset("images/logo.png") }}';
const POSTER_EVENTS = @json($monthEvents->map(function($ev) {
    $date = $ev->start_date->format('j');
    if ($ev->end_date && !$ev->end_date->eq($ev->start_date)) {
        $date = $ev->start_date->format('j') . '–' . $ev->end_date->format('j');
    }
    return ['date' => $date, 'title' => strtoupper($ev->title)];
}));

let posterColor = '#5c2e00';
let logoImg     = null;

(function preloadLogo() {
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload  = () => { logoImg = img; renderPosterPreview(); };
    img.onerror = () => { logoImg = null; renderPosterPreview(); };
    img.src = POSTER_LOGO + '?v=' + Date.now();
})();

function openExportModal() {
    document.getElementById('export-modal').classList.remove('hidden');
    setPosterColor('#5c2e00');
}
function setPosterColor(hex) {
    posterColor = hex;
    document.querySelectorAll('.swatch').forEach(s => {
        s.style.outline      = s.dataset.hex === hex ? '3px solid #000' : 'none';
        s.style.outlineOffset = '2px';
    });
    renderPosterPreview();
}

// ---- colour helpers ----
function hexToRgb(hex) {
    hex = hex.replace('#','');
    if (hex.length === 3) hex = hex.split('').map(c=>c+c).join('');
    return { r:parseInt(hex.slice(0,2),16), g:parseInt(hex.slice(2,4),16), b:parseInt(hex.slice(4,6),16) };
}
function darkenHex(hex, pct) {
    const {r,g,b} = hexToRgb(hex); const f = 1-pct/100;
    return `rgb(${Math.round(r*f)},${Math.round(g*f)},${Math.round(b*f)})`;
}

// ---- Social media icon drawers ----
// social icons — all use theme colour as background, white glyph inside
function socialIcon(ctx, cx, cy, r, theme, glyphFn) {
    ctx.save();
    // circle bg: slightly lighter/transparent overlay on theme
    ctx.fillStyle = 'rgba(255,255,255,0.20)';
    ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI*2); ctx.fill();
    ctx.fillStyle = '#fff'; ctx.strokeStyle = '#fff';
    glyphFn(ctx, cx, cy, r);
    ctx.restore();
}

// ---- draw spaced text so it spans a target pixel width ----
function fillSpacedText(ctx, text, cx, y, targetW) {
    const chars  = text.split('');
    const natural = ctx.measureText(text).width;
    const extra   = (targetW - natural) / Math.max(chars.length - 1, 1);
    let x = cx - targetW/2;
    chars.forEach(ch => {
        ctx.fillText(ch, x, y);
        x += ctx.measureText(ch).width + extra;
    });
}

// ---- main poster render ----
function drawPoster(canvas, W, H, theme, monthName, yearStr, events, verse, logo, font) {
    const ctx = canvas.getContext('2d');
    canvas.width = W; canvas.height = H;
    ctx.clearRect(0, 0, W, H);

    const headFont  = `"${font}", Impact, Arial Black, sans-serif`;
    const bodyFont  = `Arial, sans-serif`;

    // Background
    ctx.fillStyle = '#f5ede0';
    ctx.fillRect(0, 0, W, H);
    const bgG = ctx.createRadialGradient(W/2, H*0.38, 0, W/2, H*0.38, W*0.9);
    bgG.addColorStop(0, 'rgba(255,255,255,0.4)');
    bgG.addColorStop(1, 'rgba(150,110,70,0.12)');
    ctx.fillStyle = bgG; ctx.fillRect(0, 0, W, H);

    // Year tag
    ctx.save();
    ctx.fillStyle = theme;
    ctx.font = `bold ${Math.round(W*0.036)}px ${bodyFont}`;
    ctx.textAlign = 'right'; ctx.textBaseline = 'top';
    ctx.fillText('#' + yearStr, W - Math.round(W*0.044), Math.round(H*0.022));
    ctx.restore();

    // Logo
    const logoH = Math.round(H*0.088), logoTop = Math.round(H*0.032);
    if (logo) {
        const lw = logoH * (logo.naturalWidth / logo.naturalHeight);
        ctx.drawImage(logo, W/2 - lw/2, logoTop, lw, logoH);
    }

    // Church name
    const nameY = logoTop + logoH + Math.round(H*0.022);
    let nameSize = Math.round(W*0.076);
    ctx.font = `900 ${nameSize}px ${headFont}`;
    while (ctx.measureText('CHRISCO UPPER ROOM FELLOWSHIP').width > W*0.92 && nameSize > 16) {
        nameSize -= 2; ctx.font = `900 ${nameSize}px ${headFont}`;
    }
    const nameW = ctx.measureText('CHRISCO UPPER ROOM FELLOWSHIP').width;
    ctx.save();
    ctx.fillStyle = theme; ctx.textBaseline = 'alphabetic';
    ctx.fillText('CHRISCO UPPER ROOM FELLOWSHIP', W/2 - nameW/2, nameY + nameSize);
    ctx.restore();

    // Tagline — letter-spaced to match church name width exactly
    const tagY = nameY + nameSize + Math.round(H*0.01);
    let tagSize = Math.round(W*0.023);
    ctx.font = `bold ${tagSize}px ${bodyFont}`;
    ctx.save();
    ctx.fillStyle = theme; ctx.textBaseline = 'alphabetic';
    fillSpacedText(ctx, 'WHERE GOD DWELLS', W/2, tagY + tagSize, nameW);
    ctx.restore();

    // Divider
    const divY = tagY + tagSize + Math.round(H*0.016);
    ctx.save(); ctx.strokeStyle = theme; ctx.lineWidth = Math.round(W*0.002);
    ctx.beginPath(); ctx.moveTo(W/2 - nameW/2, divY); ctx.lineTo(W/2 + nameW/2, divY); ctx.stroke();
    ctx.restore();

    // Content zone
    const contentTop = divY + Math.round(H*0.018);
    const contentBot = H - Math.round(H*0.115);
    const contentH   = contentBot - contentTop;
    const leftW      = Math.round(W*0.28);
    const rightX     = leftW + Math.round(W*0.06);
    const rightW     = W - rightX - Math.round(W*0.04);
    const mCX        = leftW/2, mCY = contentTop + contentH/2;

    // 3-D Month name (rotated)
    ctx.save();
    ctx.translate(mCX, mCY); ctx.rotate(-Math.PI/2);
    let mSize = Math.round(H*0.22);
    ctx.font = `900 ${mSize}px ${headFont}`;
    while (ctx.measureText(monthName).width > contentH*0.92 && mSize > 28) {
        mSize -= 4; ctx.font = `900 ${mSize}px ${headFont}`;
    }
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    const depth = Math.round(mSize*0.055), shadow = darkenHex(theme, 40);
    for (let i = depth; i >= 1; i--) { ctx.fillStyle = shadow; ctx.fillText(monthName, i, i); }
    ctx.fillStyle = theme; ctx.fillText(monthName, 0, 0);
    ctx.fillStyle = 'rgba(255,255,255,0.22)'; ctx.fillText(monthName, -2, -2);
    ctx.restore();

    // Bible verse (vertical)
    if (verse && verse.trim()) {
        ctx.save();
        ctx.translate(leftW + Math.round(W*0.008), mCY); ctx.rotate(-Math.PI/2);
        let vSize = Math.round(W*0.018);
        ctx.font = `600 ${vSize}px ${bodyFont}`;
        while (ctx.measureText(verse).width > contentH*0.76 && vSize > 9) {
            vSize--; ctx.font = `600 ${vSize}px ${bodyFont}`;
        }
        ctx.fillStyle = darkenHex(theme, 18);
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillText(verse, 0, 0);
        ctx.restore();
    }

    // Events list
    const boxSz  = Math.round(Math.min(contentH / Math.max(events.length,1) * 0.72, W*0.082));
    const rowGap = Math.round(boxSz*0.28);
    const totalEvH = events.length*boxSz + (events.length-1)*rowGap;
    let evY = mCY - totalEvH/2;
    const rr = Math.round(boxSz*0.13);

    events.forEach(ev => {
        // Box
        ctx.save(); ctx.fillStyle = theme;
        roundRect(ctx, rightX, evY, boxSz, boxSz, rr); ctx.fill(); ctx.restore();
        // Date
        const ds = String(ev.date), dfz = ds.length > 3 ? Math.round(boxSz*0.3) : Math.round(boxSz*0.44);
        ctx.save(); ctx.fillStyle = '#fff';
        ctx.font = `bold ${dfz}px ${headFont}`;
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillText(ds, rightX + boxSz/2, evY + boxSz/2); ctx.restore();
        // Title
        let tf = Math.round(boxSz*0.37);
        ctx.font = `bold ${tf}px ${bodyFont}`;
        const maxTW = rightW - boxSz - Math.round(W*0.028);
        while (ctx.measureText(ev.title).width > maxTW && tf > 10) { tf--; ctx.font = `bold ${tf}px ${bodyFont}`; }
        ctx.save(); ctx.fillStyle = theme;
        ctx.textAlign = 'left'; ctx.textBaseline = 'middle';
        ctx.fillText(ev.title, rightX + boxSz + Math.round(W*0.026), evY + boxSz/2); ctx.restore();
        evY += boxSz + rowGap;
    });

    // ---- Bottom bar with social icons ----
    const barH = Math.round(H*0.10), barY = H - barH;
    ctx.fillStyle = theme; ctx.fillRect(0, barY, W, barH);

    const iconR  = Math.round(barH*0.13);   // small — fits comfortably
    const iconCY = barY + barH/2;
    const iGap   = Math.round(W*0.007);     // gap between icons
    const iStep  = iconR*2 + iGap;
    const tSep   = Math.round(W*0.018);     // gap between icon group and label
    const labelSz = Math.round(W*0.019);
    const pad    = Math.round(W*0.038);

    // helper: draw one themed icon (white glyph on semi-transparent circle)
    const ti = (cx, cy, fn) => socialIcon(ctx, cx, cy, iconR, theme, fn);

    // measure label widths first so we can centre the whole bar
    ctx.font = `bold ${labelSz}px ${bodyFont}`;
    const lblLeft  = 'ChrisCo Upper Room Fellowship';
    const lblRight = 'ChrisCo Upper Room';
    const lwL = ctx.measureText(lblLeft).width;
    const lwR = ctx.measureText(lblRight).width;

    // Left group total width: 4 icons + 3 gaps + sep + label
    const leftTotalW  = 4*iStep - iGap + tSep + lwL;
    // Right group total width: label + sep + 2 icons + gap
    const rightTotalW = lwR + tSep + 2*iStep - iGap;

    // Start x positions so both groups are near the edges with padding
    let lx = pad;
    // FB
    ti(lx + iconR, iconCY, (c,cx,cy,r) => {
        c.font=`bold ${Math.round(r*1.25)}px serif`;
        c.textAlign='center'; c.textBaseline='middle'; c.fillText('f', cx+r*0.08, cy+r*0.05);
    }); lx += iStep;
    // Instagram
    ti(lx + iconR, iconCY, (c,cx,cy,r) => {
        const s=r*0.54, rd=s*0.3, lw=r*0.12;
        c.lineWidth=lw; c.strokeStyle='#fff';
        c.beginPath();
        c.moveTo(cx-s+rd,cy-s); c.arcTo(cx+s,cy-s,cx+s,cy+s,rd);
        c.arcTo(cx+s,cy+s,cx-s,cy+s,rd); c.arcTo(cx-s,cy+s,cx-s,cy-s,rd);
        c.arcTo(cx-s,cy-s,cx+s,cy-s,rd); c.closePath(); c.stroke();
        c.beginPath(); c.arc(cx,cy,s*0.42,0,Math.PI*2); c.stroke();
        c.beginPath(); c.arc(cx+s*0.52,cy-s*0.52,r*0.09,0,Math.PI*2); c.fill();
    }); lx += iStep;
    // @
    ti(lx + iconR, iconCY, (c,cx,cy,r) => {
        c.font=`bold ${Math.round(r*1.05)}px Arial`;
        c.textAlign='center'; c.textBaseline='middle'; c.fillText('@',cx,cy+r*0.03);
    }); lx += iStep;
    // YouTube
    ti(lx + iconR, iconCY, (c,cx,cy,r) => {
        const tw=r*0.48, th=r*0.55;
        c.beginPath();
        c.moveTo(cx-tw*0.35,cy-th*0.5); c.lineTo(cx+tw*0.65,cy); c.lineTo(cx-tw*0.35,cy+th*0.5);
        c.closePath(); c.fill();
    }); lx += iStep + tSep;
    // Left label
    ctx.save(); ctx.fillStyle='#fff'; ctx.font=`bold ${labelSz}px ${bodyFont}`;
    ctx.textAlign='left'; ctx.textBaseline='middle';
    ctx.fillText(lblLeft, lx, iconCY); ctx.restore();

    // Right group — work right-to-left
    let rx = W - pad;
    // TikTok
    rx -= iconR;
    ti(rx, iconCY, (c,cx,cy,r) => {
        const b=r*0.28, sw=r*0.13, nx=cx-r*0.04, ny=cy+r*0.2;
        c.beginPath(); c.ellipse(nx,ny,b,b*0.78,0,0,Math.PI*2); c.fill();
        c.fillRect(nx+b-sw/2, ny-r*0.78, sw, r*0.78);
        c.lineWidth=sw; c.strokeStyle='#fff';
        c.beginPath();
        c.moveTo(nx+b+sw/2,ny-r*0.78);
        c.bezierCurveTo(nx+b+sw/2+r*0.32,ny-r*0.78, nx+b+sw/2+r*0.32,ny-r*0.35, nx+b+sw/2,ny-r*0.35);
        c.stroke();
    }); rx -= iStep;
    // X
    ti(rx, iconCY, (c,cx,cy,r) => {
        const s=r*0.46; c.lineWidth=r*0.18; c.lineCap='round';
        c.beginPath(); c.moveTo(cx-s,cy-s); c.lineTo(cx+s,cy+s); c.stroke();
        c.beginPath(); c.moveTo(cx+s,cy-s); c.lineTo(cx-s,cy+s); c.stroke();
    }); rx -= iconR + tSep;
    // Right label
    ctx.save(); ctx.fillStyle='#fff'; ctx.font=`bold ${labelSz}px ${bodyFont}`;
    ctx.textAlign='right'; ctx.textBaseline='middle';
    ctx.fillText(lblRight, rx, iconCY); ctx.restore();
}

// ---- round rect helper ----
function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x+r, y);
    ctx.arcTo(x+w, y, x+w, y+h, r); ctx.arcTo(x+w, y+h, x, y+h, r);
    ctx.arcTo(x,   y+h, x, y,   r); ctx.arcTo(x,   y,   x+w, y, r);
    ctx.closePath();
}

function renderPosterPreview() {
    const canvas = document.getElementById('poster-preview');
    const verse  = document.getElementById('poster-verse').value;
    const font   = document.getElementById('poster-font').value;
    drawPoster(canvas, 1080, 1350, posterColor, POSTER_MONTH, POSTER_YEAR, POSTER_EVENTS, verse, logoImg, font);
}

function downloadPoster() {
    const verse  = document.getElementById('poster-verse').value;
    const font   = document.getElementById('poster-font').value;
    const canvas = document.createElement('canvas');
    drawPoster(canvas, 1080, 1350, posterColor, POSTER_MONTH, POSTER_YEAR, POSTER_EVENTS, verse, logoImg, font);
    const a = document.createElement('a');
    a.download = 'chrisco-' + POSTER_MONTH.toLowerCase() + '-' + POSTER_YEAR + '.png';
    a.href = canvas.toDataURL('image/png');
    a.click();
}
</script>
@endpush
@endsection
