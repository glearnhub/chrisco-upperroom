<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sermon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SermonImportController extends Controller
{
    public function showForm()
    {
        return view('admin.sermons.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $path = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Remove header row
        array_shift($rows);

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        $validCategories = array_keys(Sermon::CATEGORIES);

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $title      = trim($row['A'] ?? '');
            $speaker    = trim($row['B'] ?? '');
            $date       = trim($row['C'] ?? '');
            $category   = strtolower(str_replace(' ', '-', trim($row['D'] ?? '')));
            $scripture  = trim($row['E'] ?? '');
            $videoUrl   = trim($row['F'] ?? '');
            $audioUrl   = trim($row['G'] ?? '');
            $description = trim($row['H'] ?? '');
            $status     = strtolower(trim($row['I'] ?? 'draft'));

            if (empty($title) || empty($speaker) || empty($date)) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Title, Speaker and Date are required — skipped.";
                continue;
            }

            // Validate date
            try {
                $parsedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Invalid date \"{$date}\" - skipped.";
                continue;
            }

            if (!in_array($status, ['published', 'draft'])) {
                $status = 'draft';
            }

            if (!in_array($category, $validCategories)) {
                $category = null;
            }

            Sermon::create([
                'title'       => $title,
                'speaker'     => $speaker,
                'sermon_date' => $parsedDate,
                'category'    => $category ?: null,
                'scripture'   => $scripture ?: null,
                'video_url'   => $videoUrl ?: null,
                'audio_url'   => $audioUrl ?: null,
                'description' => $description ?: null,
                'status'      => $status,
            ]);

            $imported++;
        }

        $message = "{$imported} sermon(s) imported successfully.";
        if ($skipped) {
            $message .= " {$skipped} row(s) skipped.";
        }

        return redirect()->route('admin.sermons.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Title', 'Speaker', 'Date (YYYY-MM-DD)', 'Category', 'Scripture', 'Video URL', 'Audio URL', 'Description', 'Status'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('0a1f44');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
        }

        // Sample row
        $sheet->setCellValue('A2', 'Walking in the Spirit');
        $sheet->setCellValue('B2', 'Apostle Das');
        $sheet->setCellValue('C2', date('Y-m-d'));
        $sheet->setCellValue('D2', 'sunday-service');
        $sheet->setCellValue('E2', 'Galatians 5:16');
        $sheet->setCellValue('F2', 'https://youtu.be/example');
        $sheet->setCellValue('G2', '');
        $sheet->setCellValue('H2', 'A message on walking in the Spirit.');
        $sheet->setCellValue('I2', 'published');

        // Category note
        $categories = implode(', ', array_keys(Sermon::CATEGORIES));
        $sheet->setCellValue('A4', 'Valid categories: ' . $categories);
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setColor(
            (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('888888')
        );

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'sermons-import-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
