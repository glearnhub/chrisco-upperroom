{{-- ═══════════════════════════════════════════════════════════════
     Universal Print Column Picker — included once in admin layout
     ═══════════════════════════════════════════════════════════════ --}}

{{-- Overlay --}}
<div id="print-dialog-overlay" style="display:none; position:fixed; inset:0; background:rgba(10,31,68,0.65); z-index:9999; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(3px);">

    <div style="background:white; border-radius:16px; box-shadow:0 24px 80px rgba(10,31,68,0.35); width:100%; max-width:640px; max-height:92vh; overflow:hidden; display:flex; flex-direction:column;">

        {{-- ── Header ── --}}
        <div style="background:linear-gradient(135deg,#0a1f44 0%,#1a3a6e 100%); padding:20px 24px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; background:rgba(240,165,0,0.2); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-print" style="color:#f0a500; font-size:18px;"></i>
                </div>
                <div>
                    <p style="color:white; font-weight:700; font-size:16px; line-height:1.2;" id="pd-report-title">Print Report</p>
                    <p style="color:rgba(255,255,255,0.6); font-size:12px; margin-top:2px;">Select columns to include</p>
                </div>
            </div>
            <button onclick="closePrintDialog()" style="width:32px; height:32px; background:rgba(255,255,255,0.12); border:none; border-radius:8px; color:white; cursor:pointer; font-size:15px; display:flex; align-items:center; justify-content:center; transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.22)'" onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- ── Body ── --}}
        <div style="padding:20px 24px; overflow-y:auto; flex:1;">

            {{-- Quick-select bar --}}
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #e5e7eb;">
                <span style="font-size:12px; color:#6b7280; font-weight:600; margin-right:4px;">Quick:</span>
                <button onclick="printDialogSelectAll(true)"
                        style="display:inline-flex; align-items:center; gap:5px; border:1.5px solid #0a1f44; background:white; color:#0a1f44; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s;"
                        onmouseover="this.style.background='#0a1f44';this.style.color='white';"
                        onmouseout="this.style.background='white';this.style.color='#0a1f44';">
                    <i class="fas fa-check-double"></i> Select All
                </button>
                <button onclick="printDialogSelectAll(false)"
                        style="display:inline-flex; align-items:center; gap:5px; border:1.5px solid #9ca3af; background:white; color:#6b7280; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s;"
                        onmouseover="this.style.background='#f3f4f6';"
                        onmouseout="this.style.background='white';">
                    <i class="fas fa-times"></i> Clear All
                </button>
                <span style="margin-left:auto; font-size:12px; color:#6b7280;" id="pd-count-label">All selected</span>
            </div>

            {{-- Column chip grid — populated by JS --}}
            <div id="print-col-grid"></div>
        </div>

        {{-- ── Footer ── --}}
        <div style="padding:16px 24px; border-top:1px solid #e5e7eb; background:#f9fafb; border-radius:0 0 16px 16px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; gap:10px;">
            <p style="font-size:11px; color:#9ca3af;">
                <i class="fas fa-info-circle" style="margin-right:4px;"></i>
                Opens a clean print window — use browser's Save as PDF option
            </p>
            <div style="display:flex; gap:10px;">
                <button onclick="closePrintDialog()"
                        style="border:1.5px solid #d1d5db; background:white; color:#374151; padding:9px 20px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; transition:background .15s;"
                        onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='white'">
                    Cancel
                </button>
                <button onclick="executePrint()"
                        style="background:linear-gradient(135deg,#0a1f44,#1a3a6e); color:white; border:none; padding:9px 24px; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:7px; box-shadow:0 2px 8px rgba(10,31,68,0.3); transition:opacity .15s;"
                        onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-print"></i> Print / PDF
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pd-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    user-select: none;
    transition: border-color .15s, background .15s, box-shadow .15s;
    background: white;
}
.pd-chip:hover { border-color: #0a1f44; background: #f0f4ff; }
.pd-chip.pd-checked { border-color: #0a1f44; background: #eef2ff; box-shadow: 0 0 0 2px rgba(10,31,68,0.08); }
.pd-chip input[type=checkbox] { display:none; }
.pd-chip-icon {
    width: 20px; height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 5px;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
    flex-shrink: 0;
    font-size: 10px;
    color: transparent;
}
.pd-chip.pd-checked .pd-chip-icon {
    background: #0a1f44;
    border-color: #0a1f44;
    color: white;
}
.pd-chip-label { font-size: 13px; font-weight: 500; color: #374151; }
.pd-chip.pd-checked .pd-chip-label { color: #0a1f44; font-weight: 600; }
</style>

<script>
(function () {
    var _tableId       = null;
    var _tableSelector = null;
    var _multiMode     = false;
    var _reportTitle   = '';
    var _subtitle      = '';

    /* ── Public API ─────────────────────────────────────────────── */

    window.openPrintDialog = function (tableId, reportTitle, subtitle) {
        _tableId       = tableId;
        _tableSelector = null;
        _multiMode     = false;
        _reportTitle   = reportTitle || '';
        _subtitle      = subtitle   || '';
        var tbl = document.querySelector(tableId);
        if (!tbl) { alert('No data table found on this page.'); return; }
        buildChips(extractHeaders(tbl));
        openDialog();
    };

    window.openPrintDialogMulti = function (tableSelector, reportTitle, subtitle) {
        _tableId       = null;
        _tableSelector = tableSelector;
        _multiMode     = true;
        _reportTitle   = reportTitle || '';
        _subtitle      = subtitle   || '';
        var first = document.querySelector(tableSelector);
        if (!first) { alert('No data table found on this page.'); return; }
        buildChips(extractHeaders(first));
        openDialog();
    };

    /* Strip <i> icon text — clone th, remove all <i> tags, read textContent */
    function extractHeaders(table) {
        var ths = table.querySelectorAll('thead tr th');
        return Array.prototype.slice.call(ths).map(function (th) {
            var clone = th.cloneNode(true);
            clone.querySelectorAll('i, svg, span.sr-only').forEach(function (el) { el.remove(); });
            var text = clone.textContent.replace(/\s+/g, ' ').trim();
            return text || ('Col ' + (Array.prototype.indexOf.call(ths, th) + 1));
        });
    }

    function buildChips(headers) {
        var grid = document.getElementById('print-col-grid');
        var twoCol = headers.length > 5;
        grid.style.cssText = twoCol
            ? 'display:grid; grid-template-columns:1fr 1fr; gap:8px;'
            : 'display:flex; flex-direction:column; gap:8px;';
        grid.innerHTML = '';

        headers.forEach(function (label, i) {
            var chip = document.createElement('label');
            chip.className = 'pd-chip pd-checked';
            chip.setAttribute('data-idx', i);
            chip.innerHTML =
                '<input type="checkbox" checked data-col-index="' + i + '">' +
                '<span class="pd-chip-icon"><i class="fas fa-check"></i></span>' +
                '<span class="pd-chip-label">' + escHtml(label) + '</span>';
            chip.addEventListener('click', function (e) {
                e.preventDefault();
                var cb = chip.querySelector('input');
                cb.checked = !cb.checked;
                chip.classList.toggle('pd-checked', cb.checked);
                updateCount();
            });
            grid.appendChild(chip);
        });
        updateCount();
        document.getElementById('pd-report-title').textContent = _reportTitle || 'Print Report';
    }

    function updateCount() {
        var total   = document.querySelectorAll('#print-col-grid input').length;
        var checked = document.querySelectorAll('#print-col-grid input:checked').length;
        var label   = document.getElementById('pd-count-label');
        if (!label) return;
        label.textContent = checked + ' of ' + total + ' column' + (total !== 1 ? 's' : '') + ' selected';
        label.style.color = checked === 0 ? '#c0392b' : '#6b7280';
    }

    window.printDialogSelectAll = function (state) {
        document.querySelectorAll('#print-col-grid input[type=checkbox]').forEach(function (cb) {
            cb.checked = state;
            var chip = cb.closest('.pd-chip');
            if (chip) chip.classList.toggle('pd-checked', state);
        });
        updateCount();
    };

    window.closePrintDialog = function () {
        document.getElementById('print-dialog-overlay').style.display = 'none';
    };

    function openDialog() {
        document.getElementById('print-dialog-overlay').style.display = 'flex';
    }

    /* ── Execute Print ──────────────────────────────────────────── */

    window.executePrint = function () {
        var selected = [];
        document.querySelectorAll('#print-col-grid input[type=checkbox]').forEach(function (cb) {
            if (cb.checked) selected.push(parseInt(cb.dataset.colIndex, 10));
        });
        if (selected.length === 0) {
            alert('Please select at least one column to print.');
            return;
        }

        /* Collect header labels for selected columns */
        var colLabels = [];
        document.querySelectorAll('#print-col-grid .pd-chip').forEach(function (chip) {
            var idx = parseInt(chip.dataset.idx, 10);
            if (selected.indexOf(idx) !== -1) {
                colLabels.push(chip.querySelector('.pd-chip-label').textContent.trim());
            }
        });

        /* Build tbody HTML */
        var tbody = '';
        var tables = _multiMode
            ? Array.prototype.slice.call(document.querySelectorAll(_tableSelector))
            : (document.querySelector(_tableId) ? [document.querySelector(_tableId)] : []);

        tables.forEach(function (tbl) {
            tbl.querySelectorAll('tbody tr').forEach(function (tr) {
                var cells = tr.querySelectorAll('td, th');
                var row = '<tr>';
                selected.forEach(function (idx) {
                    var cell = cells[idx];
                    var text = cell ? cleanText(cell) : '';
                    row += '<td>' + escHtml(text) + '</td>';
                });
                row += '</tr>';
                tbody += row;
            });
        });

        /* Clean text: strip badges/icons, get readable text */
        function cleanText(cell) {
            var clone = cell.cloneNode(true);
            clone.querySelectorAll('i, svg').forEach(function (el) { el.remove(); });
            return clone.textContent.replace(/\s+/g, ' ').trim();
        }

        var now     = new Date();
        var dateStr = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        var theadHtml = '<tr>' + colLabels.map(function (l) { return '<th>' + escHtml(l) + '</th>'; }).join('') + '</tr>';

        var html = '<!DOCTYPE html><html><head><meta charset="UTF-8">' +
            '<title>' + escHtml(_reportTitle) + '</title>' +
            '<style>' +
            '* { margin:0; padding:0; box-sizing:border-box; }' +
            'body { font-family:Arial,Helvetica,sans-serif; font-size:11px; color:#111; padding:0; }' +
            '.church-header { margin-bottom:14px; }' +
            'table { width:100%; border-collapse:collapse; margin-bottom:12px; }' +
            'thead tr { background:#0a1f44; }' +
            'thead th { padding:7px 9px; text-align:left; font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:white; }' +
            'tbody tr:nth-child(even) { background:#f1f5f9; }' +
            'tbody tr:nth-child(odd)  { background:white; }' +
            'tbody td { padding:6px 9px; border-bottom:1px solid #e2e8f0; font-size:11px; vertical-align:top; }' +
            '.footer { text-align:center; font-size:9px; color:#aaa; border-top:1px solid #e5e7eb; padding-top:8px; margin-top:4px; }' +
            '@page { margin:12mm 10mm; }' +
            '</style>' +
            '</head><body>' +
            '<div class="church-header">' +
            '<div style="display:flex;align-items:center;gap:16px;border-bottom:3px solid #0a1f44;padding-bottom:12px;margin-bottom:10px;">' +
            '<img src="{{ asset("images/logo.png") }}" style="height:60px;width:auto;object-fit:contain;flex-shrink:0;" alt="Logo">' +
            '<div style="text-align:left;">' +
            '<div style="font-size:18px;font-weight:900;color:#0a1f44;line-height:1.1;">Chrisco Upper Room Fellowship</div>' +
            '<div style="font-size:10px;color:#c0392b;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-top:2px;">Where God Dwells</div>' +
            '<div style="font-size:9.5px;color:#555;margin-top:4px;">info@chrisco-upper-room.org | +254 726 900 700 | P.O BOX 61908 Nairobi, Kenya</div>' +
            '</div></div>' +
            '<div style="text-align:center;margin-top:8px;">' +
            '<div style="font-size:14px;font-weight:800;color:#0a1f44;text-transform:uppercase;letter-spacing:.5px;">' + escHtml(_reportTitle) + '</div>' +
            (_subtitle ? '<div style="font-size:11px;color:#666;margin-top:3px;">' + escHtml(_subtitle) + '</div>' : '') +
            '<div style="font-size:10px;color:#888;margin-top:3px;">Generated: ' + dateStr + '</div>' +
            '</div>' +
            '</div>' +
            '<table><thead>' + theadHtml + '</thead><tbody>' + tbody + '</tbody></table>' +
            '<div class="footer">Chrisco Upper Room Fellowship &mdash; Where God Dwells &mdash; Nairobi, Kenya &mdash; 0726 900 700</div>' +
            '<script>window.onload=function(){window.print();window.onafterprint=function(){window.close();};};<\/script>' +
            '</body></html>';

        closePrintDialog();
        var win = window.open('', '_blank');
        if (!win) { alert('Pop-up blocked. Please allow pop-ups for this site.'); return; }
        win.document.write(html);
        win.document.close();
    };

    function escHtml(s) {
        return String(s)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* Close on backdrop click */
    document.getElementById('print-dialog-overlay').addEventListener('click', function (e) {
        if (e.target === this) closePrintDialog();
    });
})();
</script>




