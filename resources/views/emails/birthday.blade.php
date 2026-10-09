<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $greeting ?? 'Happy Birthday' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f0f4f8; font-family:Arial,Helvetica,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f4f8; padding:20px 0;">
<tr><td align="center">

<table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.10);">

    {{-- ── Header + Birthday Banner (single gradient block) ── --}}
    <tr>
        <td align="center" style="background: radial-gradient(ellipse 80% 55% at 50% 30%, #ddeaf8 0%, #9abcd8 25%, #2a5080 55%, #0a1f44 100%); padding:30px 30px 26px;">

            {{-- Logo --}}
            @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="Chrisco Upper Room" width="200" style="display:block; margin:0 auto 18px; max-width:200px; height:auto; border:0;">
            @else
            <p style="font-size:17px; font-weight:900; color:#f0a500; margin:0 0 18px; font-family:Arial,Helvetica,sans-serif;">Chrisco Upper Room Fellowship</p>
            @endif

            {{-- Confetti emoji row --}}
            <p style="font-size:20px; margin:0 0 8px; line-height:1; font-family:Arial,Helvetica,sans-serif;">🎉 &nbsp; 🎉</p>

            {{-- Birthday greeting --}}
            <p style="font-size:22px; font-weight:900; color:#f0a500; margin:0 0 10px; line-height:1.3; font-family:Arial,Helvetica,sans-serif; letter-spacing:0.5px;">
                {{ $greeting }}!
            </p>

            {{-- Celebration emojis --}}
            <p style="font-size:20px; margin:0; line-height:1; letter-spacing:8px; font-family:Arial,Helvetica,sans-serif;">🎂 &nbsp; 🥳 &nbsp; 🎁</p>

        </td>
    </tr>

    {{-- ── Body ── --}}
    <tr>
        <td style="padding:32px 36px 24px; color:#444444; font-size:15px; line-height:1.75; font-family:Arial,Helvetica,sans-serif;">

            <p style="margin:0 0 16px;">Dear <strong style="color:#0a1f44;">{{ $salutation }}</strong>,</p>

            <p style="margin:0 0 16px;">
                On behalf of the entire church family, we would like to wish you a very
                <strong style="color:#0a1f44;">Happy and Blessed Birthday!</strong> 🥳
            </p>

            <p style="margin:0 0 16px;">
                We thank God for the gift of your life and for bringing you this far.
                As you celebrate another year, may the Lord continue to guide you, strengthen you,
                protect you, and fill your life with His joy, peace, and abundant blessings.
            </p>

            <p style="margin:0 0 16px;">
                May this new year of your life bring you closer to God, open new doors,
                and give you many reasons to smile and be grateful. May the Lord grant you
                the desires of your heart according to His will and bless the work of your hands.
            </p>

            {{-- Highlight box --}}
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;">
                <tr>
                    <td style="background:#f0f6ff; border-left:4px solid #0a1f44; border-radius:6px; padding:16px 20px; font-style:italic; color:#0a1f44; font-size:14px; font-family:Arial,Helvetica,sans-serif; line-height:1.6;">
                        Please know that you are <strong>loved, valued, and an important part of our church family.</strong>
                        We celebrate you today and pray that God's grace will continue to be upon you and your family.
                    </td>
                </tr>
            </table>

            <p style="margin:0 0 16px;">
                🎂 <strong style="color:#0a1f44;">Happy Birthday once again!</strong><br>
                May you have a beautiful and joyful celebration, and may the year ahead be filled
                with God's goodness and faithfulness.
            </p>

            <p style="margin:28px 0 0;">
                <strong style="color:#0a1f44;">With love and prayers,</strong><br>
                <em>Your Church Family ❤️🙏</em>
            </p>

        </td>
    </tr>

    {{-- ── Footer ── --}}
    <tr>
        <td align="center" style="background:#0a1f44; padding:24px 30px; font-family:Arial,Helvetica,sans-serif;">
            <p style="color:#f0a500; font-weight:700; font-size:14px; margin:0 0 4px;">Chrisco Upper Room Fellowship</p>
            <p style="color:#c0392b; font-size:11px; text-transform:uppercase; letter-spacing:1px; margin:0 0 10px;">Where God Dwells</p>
            <p style="color:#afc4e8; font-size:12px; margin:0 0 4px;">P.O BOX 61908, Nairobi, Kenya &nbsp;|&nbsp; info@chrisco-upper-room.org</p>
            <p style="color:#6b8ab0; font-size:11px; margin:0;">This is an automated birthday message sent with love on your special day.</p>
        </td>
    </tr>

</table>

</td></tr>
</table>

</body>
</html>
