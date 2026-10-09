<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChurchCalendarEvent;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class ChurchCalendarImportController extends Controller
{
    public function showForm()
    {
        $categories = ChurchCalendarEvent::categories();
        return view('admin.calendar.import', compact('categories'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file'           => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'calendar_year'  => 'required|integer|min:2000|max:2100',
            'is_published'   => 'boolean',
        ]);

        $file        = $request->file('file');
        $year        = (int) $request->calendar_year;
        $published   = $request->boolean('is_published', true);
        $overwrite   = $request->boolean('overwrite', false);
        $categories  = ChurchCalendarEvent::categories();
        $categoryKeys = array_keys($categories);

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true); // assoc by col letter
        } catch (\Throwable $e) {
            return back()->withErrors(['file' => 'Could not read the file: ' . $e->getMessage()]);
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        // Detect header row — skip rows until we find "title" in column A or B
        $dataStart = 2; // default: row 1 = headers, row 2+ = data
        $firstRow  = array_values($rows)[0] ?? [];
        // normalise first row values to lowercase
        $headers   = array_map(fn($v) => strtolower(trim((string)$v)), $firstRow);

        // Map column letters to field names by header text
        $colMap = [];
        $colLetters = array_keys($firstRow);
        foreach ($colLetters as $idx => $letter) {
            $h = $headers[$idx] ?? '';
            if (str_contains($h, 'title') || str_contains($h, 'event'))       $colMap['title']    = $letter;
            elseif (str_contains($h, 'start') || $h === 'date')               $colMap['start']    = $letter;
            elseif (str_contains($h, 'end'))                                   $colMap['end']      = $letter;
            elseif (str_contains($h, 'category') || str_contains($h, 'type')) $colMap['category'] = $letter;
            elseif (str_contains($h, 'color') || str_contains($h, 'colour'))  $colMap['color']    = $letter;
            elseif (str_contains($h, 'note'))                                  $colMap['notes']    = $letter;
        }

        // Fallback positional mapping if headers not detected
        if (empty($colMap)) {
            $letters = array_keys($firstRow);
            $colMap  = [
                'title'    => $letters[0] ?? 'A',
                'start'    => $letters[1] ?? 'B',
                'end'      => $letters[2] ?? 'C',
                'category' => $letters[3] ?? 'D',
                'color'    => $letters[4] ?? 'E',
                'notes'    => $letters[5] ?? 'F',
            ];
            $dataStart = 1; // no header row — start from row 1
        }

        foreach ($rows as $rowNum => $row) {
            if ($rowNum < $dataStart) continue;

            $title = trim((string) ($row[$colMap['title']] ?? ''));
            if ($title === '') { $skipped++; continue; }

            // Parse dates — handle Excel serial numbers and string dates
            $startRaw = $row[$colMap['start']] ?? null;
            $endRaw   = $row[$colMap['end']]   ?? null;

            try {
                $startDate = $this->parseDate($startRaw, $year);
            } catch (\Throwable) {
                $errors[] = "Row {$rowNum}: invalid start date for \"{$title}\" — skipped.";
                $skipped++;
                continue;
            }

            $endDate = null;
            if ($endRaw !== null && $endRaw !== '') {
                try { $endDate = $this->parseDate($endRaw, $year); } catch (\Throwable) {}
            }
            if ($endDate && $endDate->lt($startDate)) $endDate = null;

            // Category
            $catRaw  = strtolower(trim((string) ($row[$colMap['category']] ?? 'general')));
            $category = 'general';
            foreach ($categoryKeys as $key) {
                if (str_contains($catRaw, $key) || str_contains($key, $catRaw)) {
                    $category = $key; break;
                }
            }

            // Color — sanitize to valid hex only
            $colorRaw = trim((string) ($row[$colMap['color']] ?? ''));
            if ($colorRaw && $colorRaw[0] !== '#') $colorRaw = '#' . $colorRaw;
            $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $colorRaw) ? $colorRaw : ($categories[$category]['color'] ?? '#1e3a6e');

            $notes = trim((string) ($row[$colMap['notes']] ?? ''));

            if ($overwrite) {
                // Remove existing events with same title+start_date
                ChurchCalendarEvent::where('title', $title)
                    ->where('start_date', $startDate->toDateString())
                    ->where('calendar_year', $year)
                    ->delete();
            }

            ChurchCalendarEvent::create([
                'title'         => $title,
                'start_date'    => $startDate->toDateString(),
                'end_date'      => $endDate?->toDateString(),
                'category'      => $category,
                'color'         => $color,
                'notes'         => $notes ?: null,
                'calendar_year' => $startDate->year,
                'is_published'  => $published,
            ]);

            $imported++;
        }

        $msg = "Imported {$imported} event(s).";
        if ($skipped)        $msg .= " {$skipped} row(s) skipped.";
        if (count($errors))  $msg .= ' Some rows had errors.';

        return redirect()->route('admin.calendar.index', ['year' => $year])
            ->with('success', $msg)
            ->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $categories  = ChurchCalendarEvent::categories();

        // ── Sheet 1: Import Data ────────────────────────────────────────────
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Calendar Events');

        $headers = ['Title', 'Start Date', 'End Date (optional)', 'Category', 'Color (hex)', 'Notes'];
        foreach ($headers as $i => $h) {
            $col  = chr(65 + $i);
            $cell = "{$col}1";
            $sheet->setCellValue($cell, $h);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '1E3A6E']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Example data rows
        $examples = [
            ['Sunday School Sunday',    '2026-07-26', '',            'general',  '#1e3a6e', 'Weekly children service'],
            ['Nairobi Mega Mission',    '2026-08-01', '2026-08-10', 'outreach', '#c0392b', 'Annual outreach campaign'],
            ['Handmaidens Monthly Mtg', '2026-08-23', '',            'women',    '#7c3aed', ''],
            ['Men\'s Fellowship',       '2026-08-23', '',            'men',      '#0e7490', ''],
            ['Youth Convention',        '2026-09-05', '2026-09-07', 'youth',    '#d97706', ''],
        ];
        foreach ($examples as $r => $row) {
            $rowNum = $r + 2;
            foreach ($row as $c => $val) {
                $colLetter = chr(65 + $c);
                $sheet->setCellValue("{$colLetter}{$rowNum}", $val);
            }
            // Zebra stripe
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:F{$rowNum}")->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                               'startColor' => ['rgb' => 'F0F4FA']],
                ]);
            }
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // ── Sheet 2: Colour Key ────────────────────────────────────────────
        $key = $spreadsheet->createSheet();
        $key->setTitle('Colour Key');

        // Header row
        $keyHeaders = ['Category Key', 'Label', 'Default Hex Color', 'Color Preview'];
        foreach ($keyHeaders as $i => $h) {
            $col  = chr(65 + $i);
            $cell = "{$col}1";
            $key->setCellValue($cell, $h);
            $key->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '1E3A6E']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);
            $key->getColumnDimension($col)->setWidth($i === 2 ? 18 : ($i === 3 ? 20 : 22));
        }

        $row = 2;
        foreach ($categories as $catKey => $cat) {
            $hexRaw = ltrim($cat['color'], '#');

            $key->setCellValue("A{$row}", $catKey);
            $key->setCellValue("B{$row}", $cat['label']);
            $key->setCellValue("C{$row}", $cat['color']);
            $key->setCellValue("D{$row}", '');   // preview cell — filled with the colour

            // Color swatch on the preview cell
            $key->getStyle("D{$row}")->applyFromArray([
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                           'startColor' => ['rgb' => $hexRaw]],
            ]);

            // Hex code cell: show the colour as its own background too
            $key->getStyle("C{$row}")->applyFromArray([
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $hexRaw]],
                'font'      => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ]);

            // Zebra stripe for key/label columns
            if ($row % 2 === 0) {
                $key->getStyle("A{$row}:B{$row}")->applyFromArray([
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                               'startColor' => ['rgb' => 'F5F7FB']],
                ]);
            }

            $key->getRowDimension($row)->setRowHeight(24);
            $row++;
        }

        // Note at the bottom
        $noteRow = $row + 1;
        $key->setCellValue("A{$noteRow}", '* Paste the Category Key (column A) into the "Category" column of the Calendar Events sheet.');
        $key->getStyle("A{$noteRow}")->applyFromArray([
            'font'  => ['italic' => true, 'color' => ['rgb' => '555555']],
        ]);
        $key->mergeCells("A{$noteRow}:D{$noteRow}");

        // ── Write & send ───────────────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0); // open on sheet 1
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tmp    = tempnam(sys_get_temp_dir(), 'cal_') . '.xlsx';
        $writer->save($tmp);

        return response()->download($tmp, 'chrisco-calendar-import-template.xlsx')->deleteFileAfterSend(true);
    }

    private function parseDate($value, int $fallbackYear): Carbon
    {
        if ($value === null || $value === '') throw new \InvalidArgumentException('empty');

        // Excel serial number
        if (is_numeric($value) && $value > 1000) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value));
        }

        // Try common string formats
        $formats = ['Y-m-d','d/m/Y','m/d/Y','d-m-Y','d M Y','d F Y','j M Y','j F Y'];
        foreach ($formats as $fmt) {
            try {
                $d = Carbon::createFromFormat($fmt, trim((string)$value));
                if ($d) return $d;
            } catch (\Throwable) {}
        }

        // Last resort
        return Carbon::parse((string)$value);
    }
}
