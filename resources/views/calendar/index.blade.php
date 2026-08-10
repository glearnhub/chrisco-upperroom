@extends('layouts.app')

@section('title', 'Church Calendar — Chrisco Upper Room Fellowship')

@section('content')

{{-- Hero --}}
<section style="background: #0a1f44; min-height: 180px; display:flex; align-items:center;">
    <div class="max-w-6xl mx-auto px-4 text-center w-full py-8">
        <i class="fas fa-calendar-week text-4xl sm:text-5xl mb-3" style="color: #f0a500;"></i>
        <h1 class="text-2xl sm:text-4xl font-bold text-white mb-2">Church Calendar</h1>
        <p class="text-gray-300">Stay informed about upcoming events and activities at Chrisco Upper Room Fellowship</p>

        {{-- Updates sub-nav --}}
        <div class="flex flex-wrap justify-center gap-2 mt-4">
            <a href="{{ route('events.index') }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border border-white/30 text-white hover:bg-white/20 transition">
                <i class="fas fa-calendar-alt mr-1"></i> Events
            </a>
            <a href="{{ route('announcements.index') }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border border-white/30 text-white hover:bg-white/20 transition">
                <i class="fas fa-bullhorn mr-1"></i> Announcements
            </a>
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold text-white" style="background:#f0a500;">
                <i class="fas fa-calendar-week mr-1"></i> Calendar
            </span>
        </div>
    </div>
</section>

{{-- Month navigation --}}
@php
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear  = $month == 1 ? $year - 1 : $year;
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear  = $month == 12 ? $year + 1 : $year;
@endphp

<section class="py-8 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">

        {{-- Nav bar --}}
        <div class="flex flex-wrap items-center gap-3 mb-5 bg-white rounded-xl shadow-sm border border-gray-100 p-3">
            <a href="{{ route('church.calendar', ['year' => $prevYear, 'month' => $prevMonth]) }}"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chevron-left"></i>
            </a>

            <form method="GET" action="{{ route('church.calendar') }}" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()"
                        class="border border-gray-300 rounded px-2 py-1.5 text-sm font-semibold" style="color:#1e3a6e;">
                    @foreach($months as $mn => $mname)
                    <option value="{{ $mn }}" {{ $mn == $month ? 'selected' : '' }}>{{ $mname }}</option>
                    @endforeach
                </select>
                <select name="year" onchange="this.form.submit()"
                        class="border border-gray-300 rounded px-2 py-1.5 text-sm font-semibold" style="color:#1e3a6e;">
                    @foreach(range(now()->year - 1, now()->year + 4) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('church.calendar', ['year' => $nextYear, 'month' => $nextMonth]) }}"
               class="px-3 py-1.5 rounded border border-gray-300 text-sm text-gray-600 hover:bg-gray-50">
                <i class="fas fa-chevron-right"></i>
            </a>

            <span class="text-sm text-gray-500 ml-1">{{ $monthEvents->count() }} event(s) this month</span>
        </div>

        {{-- Calendar grid --}}
        <div class="rounded-xl overflow-hidden shadow border border-gray-200 bg-white">
            {{-- Month title --}}
            <div class="px-5 py-3 text-white font-bold text-xl" style="background:#1e3a6e;">
                {{ $months[$month] }} {{ $year }}
            </div>

            {{-- Day headers --}}
            @php
                $firstDay    = \Carbon\Carbon::create($year, $month, 1);
                $daysInMonth = $firstDay->daysInMonth;
                $startDow    = $firstDay->dayOfWeek;
            @endphp
            <div style="display:grid; grid-template-columns:repeat(7,1fr);">
                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $dh)
                <div style="background:#1e3a6e; color:#fff; text-align:center; padding:10px 4px; font-size:11px; font-weight:700; text-transform:uppercase; border-right:1px solid #2d5090;">
                    {{ $dh }}
                </div>
                @endforeach

                {{-- Empty leading cells --}}
                @for($i = 0; $i < $startDow; $i++)
                <div style="border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; min-height:100px; background:#f1f3f7;"></div>
                @endfor

                {{-- Day cells --}}
                @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr   = \Carbon\Carbon::create($year, $month, $d)->toDateString();
                    $isToday   = $dateStr === now()->toDateString();
                    $dayEvents = $eventsByDate[$dateStr] ?? [];
                @endphp
                <div style="border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; min-height:100px; padding:6px; background:{{ $isToday ? '#fffde7' : '#fffff0' }}; {{ $isToday ? 'outline:2px solid #f0a500; outline-offset:-2px;' : '' }}">
                    <div style="font-size:14px; font-weight:700; color:#1e3a6e; line-height:1.2;">{{ $d }}</div>
                    @foreach($dayEvents as $ev)
                    <span style="display:block; font-size:10px; font-weight:600; line-height:1.3; padding:2px 5px; border-radius:3px; margin-top:3px; word-break:break-word; background:{{ $ev->color }}; color:#fff;">
                        {{ $ev->title }}
                    </span>
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
                    <div style="border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; min-height:100px; background:#f1f3f7;"></div>
                    @endfor
                    <div style="border-right:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; min-height:100px; padding:6px; background:#d0d8e4; font-size:11px; color:#555; font-weight:600;">Notes:</div>
                @endif
            </div>
        </div>


    </div>
</section>

@endsection
