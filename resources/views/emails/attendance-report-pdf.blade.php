<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }

    /* Cover / Header */
    .cover {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
        color: #fff;
        padding: 40px 50px 30px;
        margin-bottom: 30px;
    }
    .cover-logo-row { display: flex; align-items: center; margin-bottom: 20px; }
    .cover-logo { width: 60px; height: 60px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.4); margin-right: 16px; }
    .cover-org { font-size: 16px; font-weight: bold; letter-spacing: 0.5px; }
    .cover-tagline { font-size: 10px; opacity: 0.75; margin-top: 2px; }
    .cover-divider { border: none; border-top: 1px solid rgba(255,255,255,0.25); margin: 16px 0; }
    .cover-title { font-size: 22px; font-weight: bold; letter-spacing: 0.5px; margin-bottom: 6px; }
    .cover-subtitle { font-size: 12px; opacity: 0.85; }
    .cover-meta { margin-top: 18px; font-size: 10px; opacity: 0.7; }

    /* Body */
    .body-wrap { padding: 0 40px 40px; }

    /* Summary boxes */
    .summary-row { display: flex; gap: 16px; margin-bottom: 28px; }
    .summary-box {
        flex: 1;
        border-radius: 6px;
        padding: 14px 18px;
        border-left: 4px solid transparent;
    }
    .summary-box.inactive { background: #fff5f5; border-color: #e53e3e; }
    .summary-box.irregular { background: #fffbf0; border-color: #d69e2e; }
    .summary-box.total { background: #f0f4ff; border-color: #4361ee; }
    .summary-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; opacity: 0.7; margin-bottom: 4px; }
    .summary-box .value { font-size: 26px; font-weight: bold; line-height: 1; }
    .summary-box .desc { font-size: 10px; margin-top: 4px; opacity: 0.65; }

    /* Section heading */
    .section-heading {
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 8px 14px;
        border-radius: 4px;
        margin-bottom: 12px;
        color: #fff;
    }
    .section-heading.inactive { background: #c53030; }
    .section-heading.irregular { background: #b7791f; }
    .section-heading .count { font-size: 11px; font-weight: normal; opacity: 0.85; margin-left: 6px; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 10.5px; }
    thead tr { background: #f7f8fc; }
    th {
        text-align: left;
        padding: 8px 10px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #555;
        border-bottom: 2px solid #e2e8f0;
    }
    td { padding: 8px 10px; border-bottom: 1px solid #edf2f7; vertical-align: top; }
    tr:last-child td { border-bottom: none; }
    tr:nth-child(even) td { background: #fafbfd; }
    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 9.5px;
        font-weight: bold;
    }
    .badge-inactive { background: #fed7d7; color: #c53030; }
    .badge-irregular { background: #fefcbf; color: #b7791f; }
    td.missed { color: #c53030; font-weight: bold; }
    td.attended { color: #276749; font-weight: bold; }

    /* No-members notice */
    .empty-notice { text-align: center; padding: 22px; color: #888; font-style: italic; border: 1px dashed #ddd; border-radius: 6px; margin-bottom: 28px; }

    /* Footer */
    .report-footer {
        border-top: 1px solid #e2e8f0;
        padding-top: 14px;
        font-size: 9.5px;
        color: #888;
        display: flex;
        justify-content: space-between;
    }
    .confidential { color: #c53030; font-weight: bold; }
</style>
</head>
<body>

{{-- Cover Header --}}
<div class="cover">
    <div class="cover-logo-row">
        @php $logoPath = public_path('images/logo-email.png'); @endphp
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo" class="cover-logo">
        @endif
        <div>
            <div class="cover-org">Chrisco Upperroom Fellowship</div>
            <div class="cover-tagline">Church Management System &bull; Attendance Division</div>
        </div>
    </div>
    <hr class="cover-divider">
    <div class="cover-title">Monthly Attendance Report</div>
    <div class="cover-subtitle">Inactive &amp; Irregular Members &mdash; {{ $monthName }}</div>
    <div class="cover-meta">
        Generated: {{ $generatedAt }} &nbsp;&bull;&nbsp;
        Sundays in month: {{ $totalSundays }} &nbsp;&bull;&nbsp;
        <span style="opacity:0.9;font-weight:bold;">CONFIDENTIAL &mdash; For Internal Use Only</span>
    </div>
</div>

<div class="body-wrap">

    {{-- Summary Boxes --}}
    <div class="summary-row">
        <div class="summary-box inactive">
            <div class="label">Inactive Members</div>
            <div class="value">{{ count($inactiveMembers) }}</div>
            <div class="desc">Missed &ge; 3 Sundays</div>
        </div>
        <div class="summary-box irregular">
            <div class="label">Irregular Members</div>
            <div class="value">{{ count($irregularMembers) }}</div>
            <div class="desc">Attended only 1&ndash;2 Sundays</div>
        </div>
        <div class="summary-box total">
            <div class="label">Total Flagged</div>
            <div class="value">{{ count($inactiveMembers) + count($irregularMembers) }}</div>
            <div class="desc">Need follow-up this month</div>
        </div>
    </div>

    {{-- INACTIVE --}}
    <div class="section-heading inactive">
        Inactive Members
        <span class="count">({{ count($inactiveMembers) }} members)</span>
    </div>

    @if(count($inactiveMembers) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Office</th>
                <th style="text-align:center">Attended</th>
                <th style="text-align:center">Missed</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inactiveMembers as $i => $member)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ trim($member->name . ' ' . $member->middle_name . ' ' . $member->last_name) }}</strong></td>
                <td>{{ $member->phone ?? '—' }}</td>
                <td>{{ $member->department ?? '—' }}</td>
                <td>{{ $member->office ?? '—' }}</td>
                <td class="attended" style="text-align:center">{{ $member->sundays_attended }}</td>
                <td class="missed" style="text-align:center">{{ $member->sundays_missed }}</td>
                <td><span class="badge badge-inactive">Inactive</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-notice">No inactive members recorded for {{ $monthName }}.</div>
    @endif

    {{-- IRREGULAR --}}
    <div class="section-heading irregular">
        Irregular Members
        <span class="count">({{ count($irregularMembers) }} members)</span>
    </div>

    @if(count($irregularMembers) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Office</th>
                <th style="text-align:center">Attended</th>
                <th style="text-align:center">Missed</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($irregularMembers as $i => $member)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ trim($member->name . ' ' . $member->middle_name . ' ' . $member->last_name) }}</strong></td>
                <td>{{ $member->phone ?? '—' }}</td>
                <td>{{ $member->department ?? '—' }}</td>
                <td>{{ $member->office ?? '—' }}</td>
                <td class="attended" style="text-align:center">{{ $member->sundays_attended }}</td>
                <td class="missed" style="text-align:center">{{ $member->sundays_missed }}</td>
                <td><span class="badge badge-irregular">Irregular</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-notice">No irregular members recorded for {{ $monthName }}.</div>
    @endif

    {{-- Footer --}}
    <div class="report-footer">
        <div>Chrisco Upperroom Fellowship &mdash; Church Management System</div>
        <div class="confidential">CONFIDENTIAL &mdash; Internal Use Only</div>
        <div>Generated {{ $generatedAt }}</div>
    </div>

</div>
</body>
</html>
