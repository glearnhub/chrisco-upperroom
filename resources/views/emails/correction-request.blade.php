<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile Correction Request</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; color: #333; }
    .wrapper { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
    .header { background: #0a1f44; padding: 24px 32px; }
    .header h1 { color: #fff; margin: 0; font-size: 20px; }
    .header p { color: #f0a500; margin: 4px 0 0; font-size: 13px; }
    .badge { display: inline-block; background: #f0a500; color: #0a1f44; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; padding: 3px 10px; border-radius: 20px; margin-bottom: 16px; }
    .body { padding: 28px 32px; }
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #999; margin: 20px 0 6px; }
    .info-row { display: flex; gap: 8px; margin-bottom: 8px; font-size: 14px; }
    .info-label { color: #666; min-width: 130px; }
    .info-value { color: #111; font-weight: 600; }
    .message-box { background: #f8f9fa; border-left: 4px solid #0a1f44; border-radius: 4px; padding: 14px 16px; font-size: 14px; line-height: 1.6; margin-top: 6px; white-space: pre-wrap; }
    .action-btn { display: inline-block; margin-top: 24px; background: #0a1f44; color: #fff; text-decoration: none; padding: 11px 28px; border-radius: 6px; font-size: 14px; font-weight: 700; }
    .footer { background: #f8f9fa; border-top: 1px solid #eee; padding: 16px 32px; font-size: 12px; color: #999; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Chrisco Upper Room Fellowship</h1>
        <p>Profile Correction Request</p>
    </div>
    <div class="body">
        <span class="badge">Action Required</span>
        <p style="font-size:15px; margin:0 0 16px;">A member has submitted a request to correct their profile information.</p>

        <div class="section-title">Member Details</div>
        <div class="info-row"><span class="info-label">Full Name</span><span class="info-value">{{ $correction->user->name }} {{ $correction->user->last_name }}</span></div>
        <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ $correction->user->email }}</span></div>
        <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ $correction->user->phone ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Department</span><span class="info-value">{{ $correction->user->department ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Submitted At</span><span class="info-value">{{ $correction->created_at->format('d M Y, g:i A') }}</span></div>

        <div class="section-title">Correction Message</div>
        <div class="message-box">{{ $correction->message }}</div>

        <a href="{{ url('/admin/corrections') }}" class="action-btn">View in Admin Panel</a>
    </div>
    <div class="footer">
        This email was sent automatically by the Chrisco Upper Room Fellowship management system.<br>
        Do not reply to this email — log in to the admin panel to respond.
    </div>
</div>
</body>
</html>
