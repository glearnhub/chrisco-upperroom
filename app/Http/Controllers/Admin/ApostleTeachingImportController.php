<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApostleTeaching;
use App\Models\ApostleTeachingCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ApostleTeachingImportController extends Controller
{
    public function showForm()
    {
        return view('admin.apostle.import');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        $headers = ['Title', 'YouTube URL', 'Category', 'Description', 'Status'];
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
        $sheet->setCellValue('A2', 'Faith that Moves Mountains');
        $sheet->setCellValue('B2', 'https://youtu.be/exampleID');
        $sheet->setCellValue('C2', 'Prayer & Intercession');
        $sheet->setCellValue('D2', 'A powerful message on faith.');
        $sheet->setCellValue('E2', 'published');

        foreach (['A','B','C','D','E'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'apostle-teachings-template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
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

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 was the header

            $title      = trim($row['A'] ?? '');
            $youtubeUrl = trim($row['B'] ?? '');
            $categoryName = trim($row['C'] ?? '');
            $description = trim($row['D'] ?? '');
            $status     = strtolower(trim($row['E'] ?? 'draft'));

            if (empty($title) || empty($youtubeUrl)) {
                $skipped++;
                $errors[] = "Row {$rowNum}: Title and YouTube URL are required — skipped.";
                continue;
            }

            if (!in_array($status, ['published', 'draft'])) {
                $status = 'draft';
            }

            // Resolve or create category
            $categoryId = null;
            if ($categoryName !== '') {
                $cat = ApostleTeachingCategory::firstOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName, 'sort_order' => 0]
                );
                $categoryId = $cat->id;
            }

            ApostleTeaching::create([
                'title'       => $title,
                'youtube_url' => $youtubeUrl,
                'category_id' => $categoryId,
                'description' => $description ?: null,
                'status'      => $status,
            ]);

            $imported++;
        }

        $message = "{$imported} teaching(s) imported successfully.";
        if ($skipped) {
            $message .= " {$skipped} row(s) skipped.";
        }

        return redirect()->route('admin.apostle.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
