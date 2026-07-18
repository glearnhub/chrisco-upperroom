<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrap { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #0a1f44; padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; }
        .header p { color: #f0a500; margin: 4px 0 0; font-size: 13px; }
        .body { padding: 32px; text-align: center; }
        .otp { font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #0a1f44; margin: 24px 0; font-family: monospace; }
        .event { font-size: 14px; color: #555; margin-bottom: 8px; }
        .note { font-size: 12px; color: #999; margin-top: 20px; }
        .footer { background: #f8f8f8; padding: 16px 32px; text-align: center; font-size: 11px; color: #aaa; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <h1>Chrisco Upper Room Fellowship</h1>
        <p>Event Registration Verification</p>
    </div>
    <div class="body">
        <p class="event">Your verification code for registering to:</p>
        <p style="font-weight:bold; color:#c0392b; font-size:16px;">{{ $eventTitle }}</p>
        <div class="otp">{{ $otp }}</div>
        <p style="color:#555; font-size:14px;">Enter this code to complete your registration.</p>
        <p class="note">This code expires in <strong>10 minutes</strong>. Do not share it with anyone.</p>
        <p class="note">If you did not request this, please ignore this email.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Chrisco Upper Room Fellowship. All rights reserved.
    </div>
</div>
</body>
</html>
