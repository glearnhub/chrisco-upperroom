<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email System Test</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1A2744; padding: 28px 32px; text-align: center; }
        .header img { height: 56px; width: auto; }
        .header-text { color: #ffffff; font-size: 20px; font-weight: 700; margin-top: 12px; letter-spacing: 0.3px; }
        .header-sub { color: #93c5fd; font-size: 12px; margin-top: 4px; letter-spacing: 1px; text-transform: uppercase; }
        .body { padding: 36px 40px; }
        .badge { display: inline-block; background: #dcfce7; color: #15803d; font-size: 12px; font-weight: 700; letter-spacing: 0.8px; text-transform: uppercase; padding: 4px 12px; border-radius: 99px; margin-bottom: 20px; }
        h1 { margin: 0 0 12px; font-size: 22px; color: #1A2744; font-weight: 700; }
        p { margin: 0 0 16px; font-size: 15px; color: #475569; line-height: 1.65; }
        .detail-block { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 18px 20px; margin: 24px 0; }
        .detail-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #e2e8f0; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .detail-value { color: #334155; font-weight: 500; }
        .notice { background: #eff6ff; border-left: 3px solid #2563eb; padding: 14px 16px; border-radius: 0 6px 6px 0; font-size: 13px; color: #1e40af; margin-top: 8px; }
        .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 40px; text-align: center; }
        .footer p { font-size: 11px; color: #94a3b8; margin: 0; line-height: 1.6; }
        .footer a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="Chrisco Upperroom Fellowship">
            @else
                <div style="font-size:26px;font-weight:800;color:#fff;letter-spacing:1px;">✦ CHRISCO</div>
            @endif
            <div class="header-text">Chrisco Upperroom Fellowship</div>
            <div class="header-sub">Email System Test</div>
        </div>

        <div class="body">
            <span class="badge">✓ Delivery Confirmed</span>
            <h1>Your email system is working correctly.</h1>
            <p>This is an automated test message sent from the Chrisco Upperroom Fellowship management system to verify that outbound email delivery is configured correctly.</p>
            <p>No action is required. If you received this message, the system is ready to send notifications, OTP codes, welcome emails, and birthday greetings.</p>

            <div class="detail-block">
                <div class="detail-row">
                    <span class="detail-label">From Address</span>
                    <span class="detail-value">{{ config('mail.from.address') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sender Name</span>
                    <span class="detail-value">{{ config('mail.from.name') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">SMTP Host</span>
                    <span class="detail-value">{{ config('mail.mailers.smtp.host') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sent At</span>
                    <span class="detail-value">{{ $sentAt }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Environment</span>
                    <span class="detail-value">{{ config('app.env') }}</span>
                </div>
            </div>

            <div class="notice">
                <strong>Note for Gmail SMTP users:</strong> When sending via Gmail's SMTP server, Gmail enforces that the visible <em>From</em> address matches your authenticated Gmail account. To send mail that appears to come from <strong>info@chriscoupperroom.org</strong>, configure <em>Send mail as</em> in Gmail settings after verifying SPF/DKIM for your domain, or switch to a transactional mail service (Mailgun, SendGrid, Postmark, AWS SES).
            </div>
        </div>

        <div class="footer">
            <p>
                Chrisco Upperroom Fellowship &nbsp;|&nbsp; Management System<br>
                This is an automated system test message — please do not reply.<br>
                <a href="mailto:info@chriscoupperroom.org">info@chriscoupperroom.org</a>
            </p>
        </div>
    </div>
</body>
</html>
