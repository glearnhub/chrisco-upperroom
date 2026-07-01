<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List — Chrisco Upper Room Fellowship</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background:#f1f5f9; color:#111; }

        /* ── Picker panel ── */
        #picker {
            background: white;
            border-bottom: 3px solid #0a1f44;
            padding: 0;
        }
        .picker-header {
            background: linear-gradient(135deg,#0a1f44 0%,#1a3a6e 100%);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .picker-header-left { display: flex; align-items: center; gap: 14px; }
        .picker-header-icon {
            width: 44px; height: 44px; background: rgba(240,165,0,0.18);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
        }
        .picker-header-icon i { color: #f0a500; font-size: 20px; }
        .picker-header h2 { color: white; font-size: 17px; font-weight: 700; }
        .picker-header p  { color: rgba(255,255,255,0.6); font-size: 12px; margin-top: 2px; }

        .picker-body { padding: 20px 24px; }

        .quick-bar {
            display: flex; align-items: center; gap: 8px;
            padding-bottom: 14px; border-bottom: 1px solid #e5e7eb;
            margin-bottom: 16px;
        }
        .quick-label { font-size: 12px; color: #6b7280; font-weight: 600; margin-right: 4px; }
        .btn-quick {
            display: inline-flex; align-items: center; gap: 5px;
            border: 1.5px solid; padding: 5px 14px; border-radius: 20px;
            font-size: 12px; font-weight: 600; cursor: pointer; background: white;
            transition: all .15s;
        }
        .btn-quick-navy { border-color: #0a1f44; color: #0a1f44; }
        .btn-quick-navy:hover { background: #0a1f44; color: white; }
        .btn-quick-gray  { border-color: #9ca3af; color: #6b7280; }
        .btn-quick-gray:hover  { background: #f3f4f6; }
        #sel-count { margin-left: auto; font-size: 12px; color: #6b7280; }

        /* Chip grid */
        .col-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .pd-chip {
            display: flex; align-items: center; gap: 9px;
            padding: 9px 14px; border: 2px solid #e5e7eb; border-radius: 10px;
            cursor: pointer; user-select: none; background: white;
            transition: border-color .15s, background .15s, box-shadow .15s;
        }
        .pd-chip:hover { border-color: #0a1f44; background: #f0f4ff; }
        .pd-chip.checked { border-color: #0a1f44; background: #eef2ff; box-shadow: 0 0 0 2px rgba(10,31,68,0.08); }
        .pd-chip input { display: none; }
        .chip-box {
            width: 20px; height: 20px; border: 2px solid #d1d5db; border-radius: 5px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 10px; color: transparent; transition: all .15s;
        }
        .pd-chip.checked .chip-box { background: #0a1f44; border-color: #0a1f44; color: white; }
        .chip-label { font-size: 13px; font-weight: 500; color: #374151; }
        .pd-chip.checked .chip-label { color: #0a1f44; font-weight: 600; }

        .picker-footer {
            padding: 14px 24px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
        }
        .picker-footer-note { font-size: 11px; color: #9ca3af; }
        .picker-footer-actions { display: flex; gap: 10px; align-items: center; }
        .btn-back   { color: #6b7280; font-size: 12px; text-decoration: none; font-weight: 600; }
        .btn-cancel { border: 1.5px solid #d1d5db; background: white; color: #374151; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-print  {
            background: linear-gradient(135deg,#0a1f44,#1a3a6e); color: white; border: none;
            padding: 9px 22px; border-radius: 8px; font-size: 13px; font-weight: 700;
            cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
            box-shadow: 0 2px 8px rgba(10,31,68,0.3);
        }

        /* ── Print area ── */
        #print-area { padding: 16px; background: white; display: none; }
        .p-header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #0a1f44; margin-bottom: 12px; }
        .p-header h1 { font-size: 17px; font-weight: 900; color: #0a1f44; }
        .p-header h2 { font-size: 13px; font-weight: 700; color: #c0392b; margin-top: 4px; text-transform: uppercase; letter-spacing: .8px; }
        .p-header p  { font-size: 10px; color: #555; margin-top: 4px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0a1f44; }
        thead th { padding: 6px 8px; text-align: left; font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: white; }
        tbody tr:nth-child(even) { background: #f1f5f9; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10.5px; vertical-align: top; }
        .p-footer { margin-top: 12px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #ddd; padding-top: 6px; }

        @media print {
            #picker      { display: none !important; }
            #print-area  { display: block !important; padding: 0; }
            body         { background: white; }
            @page        { margin: 12mm 10mm; }
        }
    </style>
</head>
<body>

{{-- ── Column picker (screen only) ── --}}
<div id="picker">
    <div class="picker-header">
        <div class="picker-header-left">
            <div class="picker-header-icon"><i class="fas fa-print"></i></div>
            <div>
                <h2>Print Members List</h2>
                <p>Choose which columns to include</p>
            </div>
        </div>
    </div>

    <div class="picker-body">
        <div class="quick-bar">
            <span class="quick-label">Quick:</span>
            <button class="btn-quick btn-quick-navy" onclick="toggleAll(true)"><i class="fas fa-check-double"></i> Select All</button>
            <button class="btn-quick btn-quick-gray"  onclick="toggleAll(false)"><i class="fas fa-times"></i> Clear All</button>
            <span id="sel-count">All selected</span>
        </div>

        <div class="col-grid" id="chip-grid">
            @php
            $cols = [
                'col-no'        => '#',
                'col-name'      => 'Full Name',
                'col-gender'    => 'Gender',
                'col-marital'   => 'Marital Status',
                'col-office'    => 'Office',
                'col-phone'     => 'Phone',
                'col-email'     => 'Email',
                'col-county'    => 'County',
                'col-dept'      => 'Department 1',
                'col-dept2'     => 'Department 2',
                'col-dept3'     => 'Department 3',
                'col-cell'      => 'Home Cell',
                'col-deacon'    => 'Deacon / Deaconess',
                'col-committed' => 'Committed Member',
                'col-joined'    => 'Date Joined',
            ];
            @endphp
            @foreach($cols as $key => $label)
            <label class="pd-chip checked" data-col="{{ $key }}" onclick="toggleChip(this)">
                <input type="checkbox" checked>
                <span class="chip-box"><i class="fas fa-check"></i></span>
                <span class="chip-label">{{ $label }}</span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="picker-footer">
        <span class="picker-footer-note"><i class="fas fa-info-circle" style="margin-right:4px;"></i>Uses browser print dialog — choose "Save as PDF" to export</span>
        <div class="picker-footer-actions">
            <a href="{{ route('admin.members.index') }}" class="btn-back"><i class="fas fa-arrow-left" style="margin-right:4px;"></i>Back</a>
            <button class="btn-print" onclick="doPrint()"><i class="fas fa-print"></i> Print / PDF</button>
        </div>
    </div>
</div>

{{-- ── Print area ── --}}
<div id="print-area">
    <div class="p-header">
        <div style="display:flex; align-items:center; gap:16px; border-bottom:3px solid #0a1f44; padding-bottom:12px; margin-bottom:10px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:64px; width:auto; object-fit:contain; flex-shrink:0;">
            <div style="text-align:left;">
                <h1 style="font-size:18px; font-weight:900; color:#0a1f44; line-height:1.1; margin:0;">Chrisco Upper Room Fellowship</h1>
                <p style="font-size:10px; color:#c0392b; font-weight:700; text-transform:uppercase; letter-spacing:.8px; margin-top:2px;">Where God Dwells</p>
                <p style="font-size:9.5px; color:#555; margin-top:4px;">info@chrisco-upper-room.org &nbsp;|&nbsp; +254 726 900 700 &nbsp;|&nbsp; P.O BOX 61908 Nairobi, Kenya</p>
            </div>
        </div>
        <div style="text-align:center; margin-top:6px;">
            <h2 style="font-size:14px; font-weight:800; color:#0a1f44; text-transform:uppercase; letter-spacing:.5px; margin:0;">Members List</h2>
            <p style="font-size:10px; color:#555; margin-top:3px;">Total: {{ $members->count() }} members &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
        </div>
    </div>

    <table id="members-table">
        <thead>
            <tr>
                <th class="col-no">#</th>
                <th class="col-name">Full Name</th>
                <th class="col-gender">Gender</th>
                <th class="col-marital">Marital Status</th>
                <th class="col-office">Office</th>
                <th class="col-phone">Phone</th>
                <th class="col-email">Email</th>
                <th class="col-county">County</th>
                <th class="col-dept">Department 1</th>
                <th class="col-dept2">Department 2</th>
                <th class="col-dept3">Department 3</th>
                <th class="col-cell">Home Cell</th>
                <th class="col-deacon">Deacon / Deaconess</th>
                <th class="col-committed">Committed</th>
                <th class="col-joined">Joined</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $i => $m)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-name"><strong>{{ trim($m->name . ' ' . $m->middle_name . ' ' . $m->last_name) }}</strong></td>
                <td class="col-gender">{{ $m->gender ? ucfirst($m->gender) : '—' }}</td>
                <td class="col-marital">{{ $m->marital_status ? ucfirst($m->marital_status) : '—' }}</td>
                <td class="col-office">{{ $m->office ? ucfirst($m->office) : 'Member' }}</td>
                <td class="col-phone">{{ $m->phone ?: '—' }}</td>
                <td class="col-email" style="font-size:9px;">{{ $m->email }}</td>
                <td class="col-county">{{ $m->county ?: '—' }}</td>
                <td class="col-dept">{{ $m->department ?: '—' }}</td>
                <td class="col-dept2">{{ $m->department2 ?: '—' }}</td>
                <td class="col-dept3">{{ $m->department3 ?: '—' }}</td>
                <td class="col-cell">{{ $m->home_cell ?: '—' }}</td>
                <td class="col-deacon">{{ $m->deacon_name ?: '—' }}</td>
                <td class="col-committed">{{ $m->is_committed_member ? 'Yes' : 'No' }}</td>
                <td class="col-joined">{{ $m->membership_date ? $m->membership_date->format('M Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="p-footer">
        Chrisco Upper Room Fellowship &mdash; Where God Dwells &mdash; Nairobi, Kenya &mdash; 0726 900 700
    </div>
</div>

<script>
    function toggleChip(chip) {
        event.preventDefault();
        var cb = chip.querySelector('input');
        cb.checked = !cb.checked;
        chip.classList.toggle('checked', cb.checked);
        updateCount();
    }

    function toggleAll(state) {
        document.querySelectorAll('#chip-grid .pd-chip').forEach(function(chip) {
            chip.querySelector('input').checked = state;
            chip.classList.toggle('checked', state);
        });
        updateCount();
    }

    function updateCount() {
        var chips   = document.querySelectorAll('#chip-grid .pd-chip');
        var checked = document.querySelectorAll('#chip-grid .pd-chip.checked').length;
        var el = document.getElementById('sel-count');
        el.textContent = checked + ' of ' + chips.length + ' selected';
        el.style.color = checked === 0 ? '#c0392b' : '#6b7280';
    }

    function doPrint() {
        document.querySelectorAll('#chip-grid .pd-chip').forEach(function(chip) {
            var cls  = chip.dataset.col;
            var show = chip.classList.contains('checked');
            document.querySelectorAll('.' + cls).forEach(function(el) {
                el.style.display = show ? '' : 'none';
            });
        });
        document.getElementById('print-area').style.display = 'block';
        window.print();
    }

    window.addEventListener('afterprint', function() {
        document.getElementById('print-area').style.display = 'none';
        document.querySelectorAll('#members-table th, #members-table td').forEach(function(el) {
            el.style.display = '';
        });
    });

    updateCount();
</script>
</body>
</html>




