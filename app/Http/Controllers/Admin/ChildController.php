<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChildController extends Controller
{
    public function index(Request $request)
    {
        $query = Child::with(['parent1', 'parent2'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('first_name',    'like', "%{$s}%")
                  ->orWhere('last_name',   'like', "%{$s}%")
                  ->orWhere('parent1_name','like', "%{$s}%")
                  ->orWhere('parent2_name','like', "%{$s}%");
            });
        }

        if ($request->filled('class')) {
            $query->where('sunday_school_class', $request->class);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->export === 'csv') {
            return $this->exportCsv($query->get());
        }

        if ($request->export === 'excel') {
            return $this->exportExcel($query->get());
        }

        $children = $query->paginate(20)->withQueryString();
        $classes  = Child::sundaySchoolClasses();

        return view('admin.children.index', compact('children', 'classes'));
    }

    public function create()
    {
        $members = User::orderBy('name')->get(['id', 'name', 'phone']);
        $classes = Child::sundaySchoolClasses();
        return view('admin.children.create', compact('members', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'          => 'required|string|max:255',
            'middle_name'         => 'nullable|string|max:255',
            'last_name'           => 'required|string|max:255',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female',
            'parent1_id'          => 'nullable|exists:users,id',
            'parent1_name'        => 'nullable|string|max:255',
            'parent1_contact'     => 'nullable|string|max:20',
            'parent2_id'          => 'nullable|exists:users,id',
            'parent2_name'        => 'nullable|string|max:255',
            'parent2_contact'     => 'nullable|string|max:20',
            'sunday_school_class' => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        // If a member was selected, sync name/contact from their record
        if (!empty($validated['parent1_id'])) {
            $p = User::find($validated['parent1_id']);
            $validated['parent1_name']    = $p->name;
            $validated['parent1_contact'] = $validated['parent1_contact'] ?: $p->phone;
        }
        if (!empty($validated['parent2_id'])) {
            $p = User::find($validated['parent2_id']);
            $validated['parent2_name']    = $p->name;
            $validated['parent2_contact'] = $validated['parent2_contact'] ?: $p->phone;
        }

        $child = Child::create($validated);

        return redirect()->route('admin.children.index')
            ->with('success', "{$child->full_name} added successfully.");
    }

    public function show(Child $child)
    {
        $child->load(['parent1', 'parent2']);
        return view('admin.children.show', compact('child'));
    }

    public function edit(Child $child)
    {
        $members = User::orderBy('name')->get(['id', 'name', 'phone']);
        $classes = Child::sundaySchoolClasses();
        return view('admin.children.edit', compact('child', 'members', 'classes'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'first_name'          => 'required|string|max:255',
            'middle_name'         => 'nullable|string|max:255',
            'last_name'           => 'required|string|max:255',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female',
            'parent1_id'          => 'nullable|exists:users,id',
            'parent1_name'        => 'nullable|string|max:255',
            'parent1_contact'     => 'nullable|string|max:20',
            'parent2_id'          => 'nullable|exists:users,id',
            'parent2_name'        => 'nullable|string|max:255',
            'parent2_contact'     => 'nullable|string|max:20',
            'sunday_school_class' => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        if (!empty($validated['parent1_id'])) {
            $p = User::find($validated['parent1_id']);
            $validated['parent1_name']    = $p->name;
            $validated['parent1_contact'] = $validated['parent1_contact'] ?: $p->phone;
        }
        if (!empty($validated['parent2_id'])) {
            $p = User::find($validated['parent2_id']);
            $validated['parent2_name']    = $p->name;
            $validated['parent2_contact'] = $validated['parent2_contact'] ?: $p->phone;
        }

        $child->update($validated);

        return redirect()->route('admin.children.show', $child)
            ->with('success', 'Child record updated.');
    }

    public function destroy(Child $child)
    {
        $name = $child->full_name;
        SystemLog::record('delete', 'Children', "Child \"{$name}\" removed.");
        $child->delete();
        return redirect()->route('admin.children.index')
            ->with('success', "{$name} removed.");
    }

    // ── Exports ──────────────────────────────────────────────────────────────

    private function exportCsv($children): StreamedResponse
    {
        $filename = 'children_' . now()->format('Y-m-d') . '.csv';
        return response()->stream(function () use ($children) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['#', 'First Name', 'Middle Name', 'Last Name', 'Gender', 'Date of Birth',
                         'Sunday School Class', 'Parent 1', 'Parent 1 Contact',
                         'Parent 2', 'Parent 2 Contact', 'Notes']);
            foreach ($children as $i => $c) {
                fputcsv($h, [
                    $i + 1, $c->first_name, $c->middle_name ?? '', $c->last_name,
                    $c->gender ? ucfirst($c->gender) : '',
                    $c->date_of_birth ? $c->date_of_birth->format('d/m/Y') : '',
                    $c->sunday_school_class ?? '',
                    $c->parent1_display, $c->parent1_contact ?? '',
                    $c->parent2_display, $c->parent2_contact ?? '',
                    $c->notes ?? '',
                ]);
            }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportExcel($children)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Children');

        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Children Membership');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y') . '   |   Total: ' . count($children));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'First Name', 'Middle Name', 'Last Name', 'Gender', 'Date of Birth',
                    'Sunday School Class', 'Parent 1', 'Parent 1 Contact', 'Parent 2', 'Parent 2 Contact', 'Notes'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $h);
        }
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 5;
        foreach ($children as $i => $c) {
            $data = [
                $i + 1, $c->first_name, $c->middle_name ?? '', $c->last_name,
                $c->gender ? ucfirst($c->gender) : '',
                $c->date_of_birth ? $c->date_of_birth->format('d/m/Y') : '',
                $c->sunday_school_class ?? '',
                $c->parent1_display, $c->parent1_contact ?? '',
                $c->parent2_display, $c->parent2_contact ?? '',
                $c->notes ?? '',
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:L{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 12) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="children_' . now()->format('Y-m-d') . '.xlsx"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ── Print list ───────────────────────────────────────────────────────────

    public function printList(Request $request)
    {
        $children = Child::with(['parent1', 'parent2'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn($x) => $x->where('first_name', 'like', "%$s%")->orWhere('last_name', 'like', "%$s%"));
            })
            ->when($request->filled('class'), fn($q) => $q->where('sunday_school_class', $request->class))
            ->orderBy('first_name')->get();

        return view('admin.children.print', compact('children'));
    }

    // ── Import ───────────────────────────────────────────────────────────────

    public function importForm()
    {
        return view('admin.children.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('file')->getPathname());
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file')->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        // Skip header rows — find first row where col0 looks like a number
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $index => $row) {
            // Skip if first cell is not numeric (header rows)
            if (!is_numeric($row[0])) continue;

            $firstName = trim($row[1] ?? '');
            $lastName  = trim($row[3] ?? '');

            if (!$firstName || !$lastName) {
                $skipped++;
                continue;
            }

            try {
                Child::create([
                    'first_name'         => $firstName,
                    'middle_name'        => trim($row[2] ?? '') ?: null,
                    'last_name'          => $lastName,
                    'gender'             => strtolower(trim($row[4] ?? '')) ?: null,
                    'date_of_birth'      => !empty($row[5]) ? $this->parseDate($row[5]) : null,
                    'sunday_school_class'=> trim($row[6] ?? '') ?: null,
                    'parent1_name'       => trim($row[7] ?? '') ?: null,
                    'parent1_contact'    => trim($row[8] ?? '') ?: null,
                    'parent2_name'       => trim($row[9] ?? '') ?: null,
                    'parent2_contact'    => trim($row[10] ?? '') ?: null,
                    'notes'              => trim($row[11] ?? '') ?: null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        $msg = "{$imported} children imported successfully.";
        if ($skipped)  $msg .= " {$skipped} rows skipped (missing name).";
        if ($errors)   $msg .= " " . count($errors) . " errors.";

        return redirect()->route('admin.children.index')->with('success', $msg);
    }

    private function parseDate($val): ?string
    {
        if (!$val) return null;
        // PhpSpreadsheet may return Excel serial number or a string
        if (is_numeric($val)) {
            try {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        try {
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}

