<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Attendance Report</title>
<style>
    body { margin: 0; padding: 0; background: #f4f6f9; font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #333; }
    .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #1a1a2e, #0f3460); padding: 32px 36px; color: #fff; }
    .header-logo-row { display: flex; align-items: center; margin-bottom: 16px; }
    .header-logo { width: 52px; height: 52px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.4); margin-right: 14px; }
    .header-org { font-size: 15px; font-weight: bold; }
    .header-tagline { font-size: 11px; opacity: 0.7; margin-top: 2px; }
    .header-title { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
    .header-subtitle { font-size: 12px; opacity: 0.8; }
    .body { padding: 28px 36px; }
    p { line-height: 1.7; margin-bottom: 14px; }
    .stats-row { display: flex; gap: 14px; margin: 20px 0; }
    .stat-box { flex: 1; text-align: center; border-radius: 6px; padding: 16px 10px; }
    .stat-box.inactive { background: #fff5f5; border: 1px solid #fca5a5; }
    .stat-box.irregular { background: #fffbf0; border: 1px solid #fcd34d; }
    .stat-box .num { font-size: 32px; font-weight: bold; line-height: 1; }
    .stat-box.inactive .num { color: #c53030; }
    .stat-box.irregular .num { color: #b7791f; }
    .stat-box .lbl { font-size: 11px; color: #666; margin-top: 4px; }
    .action-note { background: #f0f4ff; border-left: 4px solid #4361ee; padding: 12px 16px; border-radius: 0 6px 6px 0; margin: 20px 0; font-size: 13px; }
    .footer { background: #f7f8fc; padding: 18px 36px; border-top: 1px solid #e2e8f0; font-size: 11px; color: #888; text-align: center; }
    .confidential { color: #c53030; font-weight: bold; }
</style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <div class="header-logo-row">
            @if(!empty($logoSrc))
                <img src="{{ $logoSrc }}" alt="Logo" class="header-logo">
            @endif
            <div>
                <div class="header-org">Chrisco Upperroom Fellowship</div>
                <div class="header-tagline">Church Management System</div>
            </div>
        </div>
        <div class="header-title">Monthly Attendance Report</div>
        <div class="header-subtitle">{{ $monthName }} &mdash; Inactive &amp; Irregular Members</div>
    </div>

    <div class="body">
        <p>Dear Admin,</p>
        <p>
            Please find attached the automated monthly attendance report for <strong>{{ $monthName }}</strong>.
            The report lists all members flagged as <strong>inactive</strong> or <strong>irregular</strong> based
            on Sunday service attendance records for the month.
        </p>

        <div class="stats-row">
            <div class="stat-box inactive">
                <div class="num">{{ $inactiveCount }}</div>
                <div class="lbl">Inactive Members<br><small>Missed &ge; 3 Sundays</small></div>
            </div>
            <div class="stat-box irregular">
                <div class="num">{{ $irregularCount }}</div>
                <div class="lbl">Irregular Members<br><small>Attended only 1&ndash;2 Sundays</small></div>
            </div>
        </div>

        <div class="action-note">
            <strong>Action Required:</strong> Please review the attached PDF and assign follow-up tasks
            to the appropriate care and follow-up team members.
        </div>

        <p>
            The full report with member details (name, phone, department, attendance breakdown) is
            attached as a PDF document.
        </p>

        <p>
            This report was automatically generated on <strong>{{ $generatedAt }}</strong> by the
            Chrisco Upperroom Fellowship Church Management System.
        </p>

        <p>God bless,<br>
        <strong>Chrisco Upperroom Fellowship</strong><br>
        <em>Church Management System &mdash; Automated Reports</em></p>
    </div>

    <div class="footer">
        <span class="confidential">CONFIDENTIAL &mdash; For Internal Use Only</span><br>
        &copy; {{ date('Y') }} Chrisco Upperroom Fellowship. This email was auto-generated; please do not reply directly.
    </div>

</div>
</body>
</html>
