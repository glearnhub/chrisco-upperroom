<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrap { max-width: 620px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #0a1f44; padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #f0a500; margin: 6px 0 0; font-size: 13px; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #333; margin-bottom: 12px; }
        .intro { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 24px; }
        table.prayers { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.prayers thead tr { background: #0a1f44; color: #fff; }
        table.prayers thead th { padding: 10px 12px; text-align: left; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }
        table.prayers tbody tr { border-bottom: 1px solid #eee; }
        table.prayers tbody tr:nth-child(even) { background: #f8fafc; }
        table.prayers tbody td { padding: 10px 12px; color: #333; vertical-align: top; }
        table.prayers tbody td.request-cell { color: #444; font-style: italic; max-width: 260px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: bold; }
        .status-pending { background: #fef9c3; color: #854d0e; }
        .status-prayed  { background: #dbeafe; color: #1e40af; }
        .status-answered{ background: #dcfce7; color: #166534; }
        .btn-wrap { text-align: center; margin: 28px 0 12px; }
        .btn { display: inline-block; padding: 12px 28px; background: #0a1f44; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: bold; }
        .note { font-size: 12px; color: #999; margin-top: 16px; line-height: 1.5; }
        .footer { background: #f8f8f8; padding: 16px 32px; text-align: center; font-size: 11px; color: #aaa; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Chrisco Upper Room Fellowship</h1>
        <p>Prayer Request Assignment</p>
    </div>
    <div class="body">
        <p class="greeting">
            Dear <strong>{{ trim($leader->name . ' ' . $leader->last_name) }}</strong>,
        </p>
        <p class="intro">
            @if($prayers->count() === 1)
                A prayer request has been assigned to you. Please pray for this person and follow up as appropriate.
            @else
                <strong>{{ $prayers->count() }} prayer requests</strong> have been assigned to you.
                Please pray for each person and follow up as appropriate.
            @endif
        </p>

        <table class="prayers">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Prayer Request</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prayers as $i => $prayer)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $prayer->is_anonymous ? 'Anonymous' : ($prayer->name ?: '—') }}</strong>
                        @if(!$prayer->is_anonymous && $prayer->email)
                        <br><span style="color:#888; font-size:11px;">{{ $prayer->email }}</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap;">{{ $prayer->phone ?: '—' }}</td>
                    <td class="request-cell">"{{ $prayer->request }}"</td>
                    <td style="white-space:nowrap; color:#888;">{{ $prayer->created_at->format('d M Y') }}</td>
                    <td>
                        @php $s = $prayer->status ?? 'pending'; @endphp
                        <span class="status-badge status-{{ $s }}">{{ ucfirst($s) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="btn-wrap">
            <a href="{{ url('/cur_admin') }}" class="btn">Go to Admin Panel</a>
        </div>

        <p class="note">
            Please log in to the admin panel to update the status of each prayer request once you have prayed.
            This is an automated notification — please do not reply to this email.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Chrisco Upper Room Fellowship. All rights reserved.
    </div>
</div>
</body>
</html>
