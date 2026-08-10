<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Church Calendar {{ $year }}</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; }
body { font-family: Arial, sans-serif; background:#fff; color:#000; font-size:11px; }
.page { padding:10px; }

/* Church header */
.church-header { text-align:center; border-bottom:3px solid #1e3a6e; padding-bottom:8px; margin-bottom:12px; }
.church-header h1 { font-size:18px; font-weight:900; color:#1e3a6e; }
.church-header h2 { font-size:13px; color:#c0392b; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-top:2px; }
.church-header p  { font-size:10px; color:#555; margin-top:3px; }
.year-title { font-size:16px; font-weight:800; color:#1e3a6e; text-align:center; margin:8px 0; text-transform:uppercase; letter-spacing:1px; }

/* One month fills the whole page */
.month-block { width:100%; border:1px solid #aaa; }
.month-title { background:#1e3a6e; color:#fff; text-align:center; padding:8px; font-weight:800; font-size:15px; letter-spacing:.5px; }

/* Calendar grid */
.cal-grid { display:grid; grid-template-columns:repeat(7,1fr); }
.cal-head  { background:#1e3a6e; color:#fff; text-align:center; padding:6px 2px; font-size:11px; font-weight:700; border-right:1px solid #2d5090; }
.cal-head:last-child { border-right:none; }
.cal-cell  { border-right:1px solid #ccc; border-bottom:1px solid #ccc; min-height:90px; padding:4px; background:#fffff0; vertical-align:top; }
.cal-cell.empty { background:#e0e4ec; }
.cal-cell.notes { background:#d0d8e4; font-size:10px; color:#444; padding:5px; font-weight:600; }
.cal-cell:last-child { border-right:none; }
.day-num { font-size:13px; font-weight:700; color:#1e3a6e; line-height:1.2; }
.ev-title { font-size:10px; font-weight:700; line-height:1.3; margin-top:3px; word-break:break-word; padding:2px 4px; border-radius:3px; display:block; color:#fff; }

/* Each month on its own page */
.month-page { page-break-after:always; }
.month-page:last-child { page-break-after:avoid; }

@page { size:A4 landscape; margin:1cm; }
@media print {
    body { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .church-header { margin-bottom:8px; }
}
</style>
</head>
<body>
<div class="page">

    @php
        $monthsToShow = $selectedMonth ? [$selectedMonth] : range(1,12);
    @endphp

    @foreach($monthsToShow as $m)
    @php
        $firstDay    = \Carbon\Carbon::create($year, $m, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDow    = $firstDay->dayOfWeek;
    @endphp
    <div class="month-page">
        {{-- Repeat church header on every page --}}
        <div class="church-header">
            @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" style="height:44px;margin-bottom:3px;">
            @endif
            <h1>Chrisco Upper Room Fellowship</h1>
            <h2>Where God Dwells</h2>
            <p>info@chrisco-upper-room.org &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; P.O BOX 61908 Nairobi, Kenya</p>
        </div>

        <div class="year-title">{{ $months[$m] }} {{ $year }} — Ministry Calendar</div>

        <div class="month-block">
            <div class="month-title">{{ $months[$m] }} {{ $year }}</div>
            <div class="cal-grid">
                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $dh)
                <div class="cal-head">{{ $dh }}</div>
                @endforeach

                @for($i = 0; $i < $startDow; $i++)
                <div class="cal-cell empty"></div>
                @endfor

                @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr   = \Carbon\Carbon::create($year, $m, $d)->toDateString();
                    $dayEvents = $eventsByDate[$dateStr] ?? [];
                @endphp
                <div class="cal-cell">
                    <div class="day-num">{{ $d }}</div>
                    @foreach($dayEvents as $ev)
                    <span class="ev-title" style="background:{{ $ev->color }};">{{ $ev->title }}</span>
                    @endforeach
                </div>
                @endfor

                @php
                    $total     = $startDow + $daysInMonth;
                    $remainder = $total % 7;
                    $trailing  = $remainder > 0 ? 7 - $remainder : 0;
                @endphp
                @if($trailing > 0)
                    @for($i = 0; $i < $trailing - 1; $i++)
                    <div class="cal-cell empty"></div>
                    @endfor
                    <div class="cal-cell notes">Notes:</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach

</div>
<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
