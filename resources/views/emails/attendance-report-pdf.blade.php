<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: "DejaVu Serif", "Times New Roman", Times, serif;
        font-size: 12pt;
        color: #222;
        background: #fff;
        margin: 1.8cm 2cm 1.8cm 2cm;
    }
    table { border-collapse: collapse; width: 100%; }
</style>
</head>
<body>

@php
    $logoPath = public_path('images/logo-email.png');
    $logoSrc  = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

{{-- Top meta bar --}}
<table style="width:100%; margin-bottom:10pt;">
    <tr>
        <td style="font-size:10pt; color:#555;">{{ now()->format('d/m/Y, H:i') }}</td>
        <td style="font-size:10pt; color:#555; text-align:center; font-weight:bold;">Attendance Report</td>
        <td style="font-size:10pt; color:#555; text-align:right;">&nbsp;</td>
    </tr>
</table>

{{-- Logo + Org header --}}
<table style="width:100%; margin-bottom:8pt;">
    <tr>
        @if($logoSrc)
        <td style="width:80pt; vertical-align:middle;">
            <img src="{{ $logoSrc }}" width="72" height="72" alt="Logo">
        </td>
        @endif
        <td style="vertical-align:middle; padding-left:10pt;">
            <div style="font-size:18pt; font-weight:bold; color:#1a1a2e; letter-spacing:0.5pt;">Chrisco Upper Room Fellowship</div>
            <div style="font-size:10pt; font-weight:bold; color:#c53030; margin-top:2pt; letter-spacing:1pt;">WHERE GOD DWELLS</div>
            <div style="font-size:10pt; color:#555; margin-top:3pt;">
                info@chriscoupperroom.org &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; P.O BOX 61908 Nairobi, Kenya
            </div>
        </td>
    </tr>
</table>

{{-- Divider --}}
<table style="width:100%; margin-bottom:12pt;">
    <tr><td style="border-top:2px solid #1a1a2e; font-size:0;">&nbsp;</td></tr>
</table>

{{-- Report Title --}}
<table style="width:100%; margin-bottom:14pt;">
    <tr>
        <td style="text-align:center;">
            <div style="font-size:13pt; font-weight:bold; color:#1a1a2e; text-transform:uppercase; letter-spacing:1pt;">
                Attendance Report &mdash; {{ $monthName }}
            </div>
            <div style="font-size:11pt; color:#555; margin-top:4pt;">
                Inactive &amp; Irregular Members &mdash; {{ count($inactiveMembers) + count($irregularMembers) }} total flagged
            </div>
            <div style="font-size:10pt; color:#888; margin-top:2pt;">
                Generated: {{ $generatedAt }}
            </div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════ INACTIVE MEMBERS ══════════════════════════ --}}
@if(count($inactiveMembers) > 0)
<table style="width:100%; margin-bottom:4pt;">
    <tr>
        <td style="font-size:11pt; font-weight:bold; color:#c53030; text-transform:uppercase; letter-spacing:0.8pt;">
            Inactive Members
            <span style="font-weight:normal; color:#888; font-size:10pt;">({{ count($inactiveMembers) }} &mdash; missed &ge; 3 Sundays)</span>
        </td>
    </tr>
</table>

<table style="width:100%; margin-bottom:22pt; font-size:11pt; table-layout:fixed;">
    <colgroup>
        <col style="width:5%">
        <col style="width:26%">
        <col style="width:17%">
        <col style="width:28%">
        <col style="width:12%">
        <col style="width:12%">
    </colgroup>
    <thead>
        <tr style="background-color:#1a1a2e; color:#ffffff;">
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">S/NO</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">Full Name</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">Phone</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">Department / Office</th>
            <th style="padding:7pt 6pt; text-align:center; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">Attended</th>
            <th style="padding:7pt 6pt; text-align:center; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt; overflow:hidden;">Missed</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inactiveMembers as $i => $member)
        @php
            $dept = trim(($member->department ?? '') . ($member->office ? ' / ' . $member->office : ''));
        @endphp
        <tr style="background-color:{{ $i % 2 === 0 ? '#ffffff' : '#f5f6fa' }};">
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#888;">{{ $i + 1 }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; font-weight:bold; color:#1a1a2e;">
                {{ trim($member->name . ' ' . ($member->middle_name ?? '') . ' ' . ($member->last_name ?? '')) }}
            </td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#444;">{{ $member->phone ?? '&mdash;' }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#1a55a3;">{{ $dept ?: '&mdash;' }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; text-align:center; color:#276749; font-weight:bold;">{{ $member->sundays_attended }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; text-align:center; color:#c53030; font-weight:bold;">{{ $member->sundays_missed }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

{{-- ══════════════════════════ IRREGULAR MEMBERS ══════════════════════════ --}}
@if(count($irregularMembers) > 0)
<table style="width:100%; margin-bottom:4pt;">
    <tr>
        <td style="font-size:11pt; font-weight:bold; color:#b7791f; text-transform:uppercase; letter-spacing:0.8pt;">
            Irregular Members
            <span style="font-weight:normal; color:#888; font-size:10pt;">({{ count($irregularMembers) }} &mdash; attended 1&ndash;2 Sundays)</span>
        </td>
    </tr>
</table>

<table style="width:100%; margin-bottom:22pt; font-size:11pt; table-layout:fixed;">
    <colgroup>
        <col style="width:5%">
        <col style="width:26%">
        <col style="width:17%">
        <col style="width:28%">
        <col style="width:12%">
        <col style="width:12%">
    </colgroup>
    <thead>
        <tr style="background-color:#1a1a2e; color:#ffffff;">
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">S/NO</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">Full Name</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">Phone</th>
            <th style="padding:7pt 6pt; text-align:left; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">Department / Office</th>
            <th style="padding:7pt 6pt; text-align:center; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">Attended</th>
            <th style="padding:7pt 6pt; text-align:center; font-size:9.5pt; text-transform:uppercase; letter-spacing:0.4pt;">Missed</th>
        </tr>
    </thead>
    <tbody>
        @foreach($irregularMembers as $i => $member)
        @php
            $dept = trim(($member->department ?? '') . ($member->office ? ' / ' . $member->office : ''));
        @endphp
        <tr style="background-color:{{ $i % 2 === 0 ? '#ffffff' : '#f5f6fa' }};">
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#888;">{{ $i + 1 }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; font-weight:bold; color:#1a1a2e;">
                {{ trim($member->name . ' ' . ($member->middle_name ?? '') . ' ' . ($member->last_name ?? '')) }}
            </td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#444;">{{ $member->phone ?? '&mdash;' }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; color:#1a55a3;">{{ $dept ?: '&mdash;' }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; text-align:center; color:#276749; font-weight:bold;">{{ $member->sundays_attended }}</td>
            <td style="padding:6pt 6pt; border-bottom:1px solid #e5e7eb; text-align:center; color:#c53030; font-weight:bold;">{{ $member->sundays_missed }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(count($inactiveMembers) === 0 && count($irregularMembers) === 0)
<table style="width:100%; margin-bottom:20pt;">
    <tr>
        <td style="padding:20pt; text-align:center; color:#aaa; font-style:italic; border:1px dashed #ddd;">
            No inactive or irregular members recorded for {{ $monthName }}.
        </td>
    </tr>
</table>
@endif

{{-- Footer --}}
<table style="width:100%; margin-top:14pt; border-top:1px solid #ccc;">
    <tr>
        <td style="padding-top:8pt; font-size:10pt; color:#888; text-align:center;">
            Chrisco Upper Room Fellowship &mdash; Where God Dwells &mdash; Nairobi, Kenya &mdash; 0726 900 700
        </td>
    </tr>
</table>

</body>
</html>
