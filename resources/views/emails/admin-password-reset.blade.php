<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Reset</title>
<style>
    body { margin: 0; padding: 0; background: #f4f6f9; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; }
    .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }

    /* Header */
    .header { background: linear-gradient(135deg, #1a1a2e, #0f3460); padding: 32px 36px; color: #fff; text-align: center; }
    .header-logo { width: 64px; height: 64px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); margin: 0 auto 14px; display: block; }
    .header-org { font-size: 13px; opacity: 0.8; margin-bottom: 14px; }
    .header-title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
    .header-subtitle { font-size: 12px; opacity: 0.75; }

    /* Body */
    .body { padding: 28px 36px; line-height: 1.7; }
    p { margin-bottom: 14px; }

    /* Credentials box */
    .credentials {
        background: #f8f9ff;
        border: 1px solid #dde3f5;
        border-left: 4px solid #4361ee;
        border-radius: 6px;
        padding: 18px 22px;
        margin: 20px 0;
    }
    .credentials-title { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #888; margin-bottom: 12px; font-weight: bold; }
    .cred-row { display: flex; margin-bottom: 8px; }
    .cred-label { width: 110px; color: #666; font-size: 13px; flex-shrink: 0; }
    .cred-value { font-weight: bold; color: #1a1a2e; font-size: 13px; font-family: 'Courier New', monospace; background: #eef0f8; padding: 2px 8px; border-radius: 4px; }

    /* Warning box */
    .warning {
        background: #fffbf0;
        border: 1px solid #fcd34d;
        border-left: 4px solid #d69e2e;
        border-radius: 6px;
        padding: 12px 16px;
        margin: 18px 0;
        font-size: 13px;
        color: #7a5a00;
    }
    .warning strong { color: #92400e; }

    /* Button */
    .btn-wrap { text-align: center; margin: 24px 0; }
    .btn {
        display: inline-block;
        background: #1a1a2e;
        color: #fff !important;
        text-decoration: none;
        padding: 13px 32px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        letter-spacing: 0.3px;
    }

    /* Footer */
    .footer { background: #f7f8fc; padding: 18px 36px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #888; text-align: center; }
</style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        @if(!empty($logoSrc))
            <img src="{{ $logoSrc }}" alt="Logo" class="header-logo">
        @endif
        <div class="header-org">Chrisco Upperroom Fellowship &mdash; Admin Panel</div>
        <div class="header-title">Your Password Has Been Reset</div>
        <div class="header-subtitle">Action required: log in and change your password immediately</div>
    </div>

    <div class="body">
        <p>Dear <strong>{{ $user->name }} {{ $user->last_name }}</strong>,</p>

        <p>
            The system administrator has reset your admin account password.
            Below are your new temporary login credentials. Please use them to
            log in and you will be required to set a new password before you can continue.
        </p>

        <div class="credentials">
            <div class="credentials-title">Your New Login Details</div>
            <div class="cred-row">
                <span class="cred-label">Email:</span>
                <span class="cred-value">{{ $user->email }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Password:</span>
                <span class="cred-value">{{ $temporaryPassword }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-label">Login URL:</span>
                <span class="cred-value" style="font-size:11px;">{{ $loginUrl }}</span>
            </div>
        </div>

        <div class="warning">
            <strong>Important:</strong> This is a temporary password. You will be forced to change it
            immediately after logging in. Do not share these credentials with anyone.
        </div>

        <div class="btn-wrap">
            <a href="{{ $loginUrl }}" class="btn">Log In &amp; Change Password &rarr;</a>
        </div>

        <p>
            If you did not expect this email or believe this was done in error,
            please contact the system administrator immediately at
            <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
        </p>

        <p>
            God bless,<br>
            <strong>Chrisco Upperroom Fellowship</strong><br>
            <em>Church Management System</em>
        </p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Chrisco Upperroom Fellowship. This is an automated security notification.
    </div>

</div>
</body>
</html>
