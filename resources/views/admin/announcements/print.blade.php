<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements — Chrisco Upper Room Fellowship</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%;
            min-height: 100%;
            background: #d6eaf8;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 30px 40px 40px;
        }
        .page {
            max-width: 680px;
            margin: 0 auto;
        }

        @page { margin: 0; size: A4; }

        /* ── Font picker toolbar ── */
        .toolbar {
            max-width: 680px;
            margin: 0 auto 24px;
            background: #1a3a6b;
            border-radius: 10px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .toolbar label {
            color: #cde6ff;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }
        .font-select {
            flex: 1;
            min-width: 180px;
            padding: 7px 12px;
            border-radius: 6px;
            border: none;
            font-size: 13px;
            background: #fff;
            color: #1a3a6b;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }
        .toolbar-actions { display: flex; gap: 8px; margin-left: auto; }
        .btn-print {
            background: #c0392b; color: #fff; border: none;
            padding: 8px 22px; border-radius: 6px; font-size: 13px;
            font-weight: 700; cursor: pointer; white-space: nowrap;
        }
        .btn-print:hover { background: #a93226; }
        .btn-close {
            background: rgba(255,255,255,0.15); color: #fff; border: none;
            padding: 8px 16px; border-radius: 6px; font-size: 13px; cursor: pointer;
        }
        .btn-close:hover { background: rgba(255,255,255,0.25); }

        /* ── Document styles ── */
        .header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 6px;
        }
        .header img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .header-text .church-name {
            font-size: 18px;
            font-weight: 900;
            color: #1a3a6b;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.2;
        }
        .header-text .sub-title {
            font-size: 13px;
            font-weight: 700;
            color: #c0392b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .date-line {
            text-align: right;
            font-size: 12px;
            color: #555;
            margin-bottom: 18px;
        }
        hr.divider {
            border: none;
            border-top: 2px solid #1a3a6b;
            margin: 10px 0 20px;
        }
        .announcement { margin-bottom: 18px; padding-left: 4px; }
        .announcement-header { display: flex; gap: 10px; align-items: flex-start; }
        .num { font-size: 14px; font-weight: 900; color: #1a3a6b; min-width: 22px; padding-top: 1px; }
        .title { font-size: 14px; font-weight: 900; color: #1a3a6b; line-height: 1.4; }
        .body { font-size: 13px; color: #333; line-height: 1.6; margin-top: 4px; padding-left: 32px; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #888; }

        @media print {
            html, body { background: #d6eaf8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            body { padding: 30px 40px 40px; }
        }
    </style>
</head>
<body>

{{-- ── Font Picker & Actions (hidden when printing) ── --}}
<div class="toolbar no-print">
    <label for="font-select">Font Style:</label>
    <select id="font-select" class="font-select" onchange="setFont(this)">
        <option value="Arial, sans-serif">Arial</option>
        <option value="'Times New Roman', Times, serif">Times New Roman</option>
        <option value="Georgia, serif">Georgia</option>
        <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
        <option value="Verdana, sans-serif">Verdana</option>
        <option value="'Courier New', Courier, monospace">Courier New</option>
    </select>
    <div class="toolbar-actions">
        <button class="btn-print" onclick="window.print()">🖨 Print / Save PDF</button>
        <button class="btn-close" onclick="window.close()">✕ Close</button>
    </div>
</div>

<div class="page">
    <div class="header">
        @php $logoPath = public_path('storage/settings/logo.png'); $logoFallback = public_path('images/logo.png'); @endphp
        @if(file_exists($logoPath))
            <img src="{{ asset('storage/settings/logo.png') }}" alt="Logo">
        @elseif(file_exists($logoFallback))
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        @else
            <div style="width:70px;height:70px;background:#1a3a6b;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                <span style="color:#fff;font-size:22px;font-weight:900;">C</span>
            </div>
        @endif
        <div class="header-text">
            <div class="church-name">Chrisco Upper Room Fellowship</div>
            <div class="sub-title">Announcements</div>
        </div>
    </div>

    <div class="date-line">{{ $dateLabel }}</div>

    <hr class="divider">

    @forelse($announcements as $i => $ann)
    <div class="announcement">
        <div class="announcement-header">
            <span class="num">{{ $i + 1 }}.</span>
            <span class="title">{{ $ann->title }}</span>
        </div>
        @if($ann->body)
        <div class="body">{{ $ann->body }}</div>
        @endif
    </div>
    @empty
    <p style="color:#888; font-style:italic; text-align:center; padding:30px 0;">No active announcements.</p>
    @endforelse

    <div class="footer">
        Printed {{ now()->format('d M Y, H:i') }} &mdash; Chrisco Upper Room Fellowship, Nairobi
    </div>
</div>

<script>
    function setFont(select) {
        document.body.style.fontFamily = select.value;
    }
</script>
</body>
</html>
