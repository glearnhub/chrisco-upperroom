<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Chrisco Upper Room Admin Panel</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f4f8; padding:20px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.10);">

    {{-- Header --}}
    <tr>
        <td align="center" style="background: radial-gradient(ellipse 80% 55% at 50% 30%, #ddeaf8 0%, #9abcd8 25%, #2a5080 55%, #0a1f44 100%); padding:30px 30px 26px;">

            @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Chrisco Upper Room" width="200" style="display:block; margin:0 auto 18px; max-width:200px; height:auto; border:0;">
            @else
            <p style="font-size:17px; font-weight:900; color:#f0a500; margin:0 0 18px; font-family:Arial,Helvetica,sans-serif;">Chrisco Upper Room Fellowship</p>
            @endif

            <p style="font-size:22px; font-weight:900; color:#f0a500; margin:0 0 6px; line-height:1.3; font-family:Arial,Helvetica,sans-serif; letter-spacing:0.5px;">
                Welcome, {{ $user->name }}!
            </p>
            <p style="font-size:14px; color:#afc4e8; margin:0; font-family:Arial,Helvetica,sans-serif;">
                Your admin account has been created
            </p>

        </td>
    </tr>

    {{-- Body --}}
    <tr>
        <td style="padding:32px 36px 24px; color:#444444; font-size:15px; line-height:1.75; font-family:Arial,Helvetica,sans-serif;">

            <p style="margin:0 0 16px;">Dear <strong style="color:#0a1f44;">{{ $user->name }} {{ $user->last_name }}</strong>,</p>

            <p style="margin:0 0 16px;">
                Your admin account for the <strong style="color:#0a1f44;">Chrisco Upper Room Fellowship</strong> management
                system has been set up. You can now log in using the details below.
            </p>

            {{-- Credentials box --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;">
                <tr>
                    <td style="background:#f0f6ff; border-left:4px solid #0a1f44; border-radius:6px; padding:18px 20px; font-family:Arial,Helvetica,sans-serif;">
                        <p style="margin:0 0 10px; font-size:13px; font-weight:700; color:#0a1f44; text-transform:uppercase; letter-spacing:0.5px;">Your Login Details</p>
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="font-size:14px; color:#555; padding:4px 0; width:90px;">Email:</td>
                                <td style="font-size:14px; color:#0a1f44; font-weight:600; padding:4px 0; font-family:monospace;">{{ $user->email }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{-- Reset link button --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
                <tr>
                    <td align="center" style="padding:10px 0;">
                        <a href="{{ $resetUrl }}" style="display:inline-block; background:#0a1f44; color:#f0a500; text-decoration:none; font-weight:700; font-size:15px; padding:14px 32px; border-radius:8px; font-family:Arial,Helvetica,sans-serif; letter-spacing:0.3px;">
                            Set Your Password &rarr;
                        </a>
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
                <tr>
                    <td style="background:#fff8e1; border-left:4px solid #f0a500; border-radius:6px; padding:14px 18px; font-size:13px; color:#7a5800; font-family:Arial,Helvetica,sans-serif; line-height:1.6;">
                        <strong>Important:</strong> This link expires in 60 minutes. Click the button above to set your password and access the admin panel.
                        If the button doesn't work, copy and paste this URL into your browser:<br>
                        <span style="font-family:monospace; font-size:12px; word-break:break-all;">{{ $resetUrl }}</span>
                    </td>
                </tr>
            </table>

            <p style="margin:0 0 16px;">
                If you have any questions or need assistance, please contact the system administrator.
            </p>

            <p style="margin:28px 0 0;">
                <strong style="color:#0a1f44;">God bless you,</strong><br>
                <em>Chrisco Upper Room Fellowship ❤️🙏</em>
            </p>

        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td align="center" style="background:#0a1f44; padding:24px 30px; font-family:Arial,Helvetica,sans-serif;">
            <p style="color:#f0a500; font-weight:700; font-size:14px; margin:0 0 4px;">Chrisco Upper Room Fellowship</p>
            <p style="color:#c0392b; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 10px;">Where God Dwells</p>
            <p style="color:#afc4e8; font-size:12px; margin:0 0 4px;">P.O BOX 61908, Nairobi, Kenya &nbsp;|&nbsp; info@chrisco-upper-room.org</p>
            <p style="color:#6b8ab0; font-size:11px; margin:0;">This is an automated message. Please do not reply to this email.</p>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>
