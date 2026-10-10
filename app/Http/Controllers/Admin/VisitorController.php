<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitor::latest('visit_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('phone',   'like', "%{$s}%")
                  ->orWhere('email',   'like', "%{$s}%");
            });
        }

        if ($request->filled('follow_up')) {
            $query->where('follow_up_status', $request->follow_up);
        }

        if ($request->filled('how_heard')) {
            $query->where('how_heard', $request->how_heard);
        }

        if ($request->export === 'excel') {
            abort_if(!auth()->user()->hasPermission('visitors.view'), 403);
            return $this->exportExcel($query->get());
        }

        $visitors = $query->paginate(20)->withQueryString();

        $stats = [
            'total'          => Visitor::count(),
            'today'          => Visitor::whereDate('visit_date', today())->count(),
            'this_month'     => Visitor::whereMonth('visit_date', now()->month)->whereYear('visit_date', now()->year)->count(),
            'pending_followup' => Visitor::where('follow_up_status', 'pending')->count(),
            'returning'      => Visitor::where('visited_before', true)->count(),
        ];

        return view('admin.visitors.index', compact('visitors', 'stats'));
    }

    public function create()
    {
        return view('admin.visitors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:200',
            'gender'              => 'nullable|in:Male,Female,Other',
            'residence'           => 'nullable|string|max:200',
            'occupation'          => 'nullable|string|max:200',
            'marital_status'      => 'nullable|in:Single,Married,Divorced,Widowed,Separated',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email|max:200',
            'preferred_contact'   => 'nullable|array',
            'preferred_contact.*' => 'in:phone,whatsapp,sms,email',
            'visited_before'      => 'nullable|boolean',
            'is_chrisco_member'   => 'nullable|boolean',
            'chrisco_church'      => 'nullable|string|max:200',
            'attends_another_church' => 'nullable|boolean',
            'another_church_name' => 'nullable|string|max:200',
            'visit_date'          => 'required|date',
            'invited_by'          => 'nullable|string|max:200',
            'how_heard'           => 'nullable|in:Friend,Family,Social Media,Website,Walk In,Evangelism,Other',
            'prayer_request'      => 'nullable|string|max:2000',
            'notes'               => 'nullable|string|max:2000',
            'consent'             => 'accepted',
        ]);

        $data['visited_before']       = $request->boolean('visited_before');
        $data['is_chrisco_member']    = $request->boolean('is_chrisco_member');
        $data['attends_another_church'] = $request->boolean('attends_another_church');

        $visitor = Visitor::create($data);

        SystemLog::record('create', 'visitors', "Added visitor: {$visitor->full_name} (#{$visitor->id})");

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor record added successfully.');
    }

    public function show(Visitor $visitor)
    {
        return view('admin.visitors.show', compact('visitor'));
    }

    public function edit(Visitor $visitor)
    {
        return view('admin.visitors.edit', compact('visitor'));
    }

    public function update(Request $request, Visitor $visitor)
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:200',
            'gender'              => 'nullable|in:Male,Female,Other',
            'residence'           => 'nullable|string|max:200',
            'occupation'          => 'nullable|string|max:200',
            'marital_status'      => 'nullable|in:Single,Married,Divorced,Widowed,Separated',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email|max:200',
            'preferred_contact'   => 'nullable|array',
            'preferred_contact.*' => 'in:phone,whatsapp,sms,email',
            'visited_before'      => 'nullable|boolean',
            'is_chrisco_member'   => 'nullable|boolean',
            'chrisco_church'      => 'nullable|string|max:200',
            'attends_another_church' => 'nullable|boolean',
            'another_church_name' => 'nullable|string|max:200',
            'visit_date'          => 'required|date',
            'invited_by'          => 'nullable|string|max:200',
            'how_heard'           => 'nullable|in:Friend,Family,Social Media,Website,Walk In,Evangelism,Other',
            'prayer_request'      => 'nullable|string|max:2000',
            'follow_up_status'    => 'nullable|in:pending,contacted,completed',
            'notes'               => 'nullable|string|max:2000',
        ]);

        $data['visited_before']       = $request->boolean('visited_before');
        $data['is_chrisco_member']    = $request->boolean('is_chrisco_member');
        $data['attends_another_church'] = $request->boolean('attends_another_church');

        $visitor->update($data);

        SystemLog::record('update', 'visitors', "Updated visitor: {$visitor->full_name} (#{$visitor->id})");

        return redirect()->route('admin.visitors.show', $visitor)
            ->with('success', 'Visitor record updated successfully.');
    }

    public function destroy(Visitor $visitor)
    {
        $name = $visitor->full_name;
        $visitor->delete();

        SystemLog::record('delete', 'visitors', "Deleted visitor: {$name}");

        return redirect()->route('admin.visitors.index')
            ->with('success', 'Visitor record deleted.');
    }

    public function importForm()
    {
        return view('admin.visitors.import');
    }

    public function importStore(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls|max:5120']);

        $reader = new XlsxReader();
        $spreadsheet = $reader->load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $imported = 0;
        $skipped  = 0;
        foreach (array_slice($rows, 2) as $row) { // skip header rows
            $name  = trim($row['A'] ?? '');
            $phone = trim($row['E'] ?? '');
            if (! $name || ! $phone) continue;

            // Skip duplicates: same phone already exists in visitors table
            if (Visitor::where('phone', $phone)->exists()) {
                $skipped++;
                continue;
            }

            Visitor::create([
                'full_name'    => $name,
                'gender'       => in_array($row['B'] ?? '', ['Male','Female','Other']) ? $row['B'] : null,
                'residence'    => $row['C'] ?? null,
                'occupation'   => $row['D'] ?? null,
                'phone'        => $phone,
                'email'        => filter_var($row['F'] ?? '', FILTER_VALIDATE_EMAIL) ? $row['F'] : null,
                'visit_date'   => today(),
                'how_heard'    => null,
            ]);
            $imported++;
        }

        SystemLog::record('import', 'visitors', "Imported {$imported} visitor records, {$skipped} skipped as duplicates.");

        $msg = "Imported {$imported} visitor records.";
        if ($skipped > 0) $msg .= " {$skipped} skipped (duplicate phone numbers already on record).";
        return redirect()->route('admin.visitors.index')->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['Full Name', 'Gender', 'Residence', 'Occupation', 'Phone', 'Email'];
        foreach (array_values($headers) as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0A1F44');
            $sheet->getStyle("{$col}1")->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getColumnDimension($col)->setWidth(20);
        }

        $sheet->setCellValue('A2', 'John Doe');
        $sheet->setCellValue('B2', 'Male');
        $sheet->setCellValue('C2', 'Nairobi');
        $sheet->setCellValue('D2', 'Engineer');
        $sheet->setCellValue('E2', '0712345678');
        $sheet->setCellValue('F2', 'john@email.com');

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="visitors-import-template.xlsx"',
        ]);
    }

    private function exportExcel($visitors): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Visitors');

        $headers = ['#', 'Full Name', 'Gender', 'Phone', 'Email', 'Visit Date', 'Visited Before', 'How Heard', 'Follow Up'];
        foreach (array_values($headers) as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
            $sheet->getStyle("{$col}1")->getFont()->setBold(true);
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0A1F44');
            $sheet->getStyle("{$col}1")->getFont()->getColor()->setARGB('FFFFFFFF');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $row = 2;
        foreach ($visitors as $i => $v) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $v->full_name);
            $sheet->setCellValue("C{$row}", $v->gender ?? '');
            $sheet->setCellValue("D{$row}", $v->phone);
            $sheet->setCellValue("E{$row}", $v->email ?? '');
            $sheet->setCellValue("F{$row}", $v->visit_date?->format('Y-m-d') ?? '');
            $sheet->setCellValue("G{$row}", $v->visited_before ? 'Yes' : 'No');
            $sheet->setCellValue("H{$row}", $v->how_heard ?? '');
            $sheet->setCellValue("I{$row}", ucfirst($v->follow_up_status));
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="visitors-' . now()->format('Y-m-d') . '.xlsx"',
        ]);
    }
}
