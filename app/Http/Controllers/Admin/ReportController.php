<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Child;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ReportController extends Controller
{
    private function officeOrderSql(): string
    {
        return "CASE office
            WHEN 'presbyter' THEN 1
            WHEN 'pastor'    THEN 2
            WHEN 'elder'     THEN 3
            WHEN 'deacon'    THEN 4
            WHEN 'deaconess' THEN 5
            ELSE 6 END";
    }

    // ── Full Membership Report ──────────────────────────────────────────────
    public function membership(Request $request)
    {
        $members = User::where('role', 'member')
            ->orderByRaw($this->officeOrderSql())
            ->orderBy('name')
            ->get();

        if ($request->export === 'excel') {
            return $this->excelMembership($members);
        }

        $grouped = $members->groupBy(fn ($m) => $m->office ? ucfirst($m->office) : 'Member');
        $officeOrder = ['Presbyter', 'Pastor', 'Elder', 'Deacon', 'Deaconess', 'Member'];
        $grouped = collect($officeOrder)
            ->filter(fn ($k) => $grouped->has($k))
            ->mapWithKeys(fn ($k) => [$k => $grouped[$k]]);

        return view('admin.reports.membership', compact('members', 'grouped'));
    }

    // ── Leaders Report ──────────────────────────────────────────────────────
    public function leaders(Request $request)
    {
        $leaders = User::whereIn('office', ['presbyter', 'pastor', 'elder', 'deacon', 'deaconess'])
            ->orderByRaw($this->officeOrderSql())
            ->orderBy('name')
            ->get();

        if ($request->export === 'excel') {
            return $this->excelLeaders($leaders);
        }

        $grouped = $leaders->groupBy(fn ($l) => ucfirst($l->office));
        $officeOrder = ['Presbyter', 'Pastor', 'Elder', 'Deacon', 'Deaconess'];
        $grouped = collect($officeOrder)
            ->filter(fn ($k) => $grouped->has($k))
            ->mapWithKeys(fn ($k) => [$k => $grouped[$k]]);

        return view('admin.reports.leaders', compact('leaders', 'grouped'));
    }

    // ── Deacon/Deaconess Mentorship Report ──────────────────────────────────
    public function mentorship(Request $request)
    {
        // All unique mentor names (from assigned members)
        $mentorNames = User::whereNotNull('deacon_name')
            ->pluck('deacon_name')->unique()->sort()->values();

        $selectedMentor = $request->mentor;
        $mentees = collect();

        if ($selectedMentor) {
            $mentees = User::where('deacon_name', $selectedMentor)
                ->orderBy('name')->get();

            if ($request->export === 'excel') {
                return $this->excelMentorship($mentees, $selectedMentor);
            }
        }

        return view('admin.reports.mentorship', compact('mentorNames', 'selectedMentor', 'mentees'));
    }

    // ── By Department Report ─────────────────────────────────────────────────
    public function byDepartment(Request $request)
    {
        // Collect unique department names across all three department fields
        $d1 = User::where('role', 'member')->whereNotNull('department')->where('department', '!=', '')->pluck('department');
        $d2 = User::where('role', 'member')->whereNotNull('department2')->where('department2', '!=', '')->pluck('department2');
        $d3 = User::where('role', 'member')->whereNotNull('department3')->where('department3', '!=', '')->pluck('department3');
        $departments = $d1->merge($d2)->merge($d3)->unique()->sort()->values();

        $selectedDept = $request->department;
        $members = collect();

        if ($selectedDept && $selectedDept !== 'all') {
            // Match members who have this dept in ANY of their three department fields
            $members = User::where('role', 'member')
                ->where(function ($q) use ($selectedDept) {
                    $q->where('department',  $selectedDept)
                      ->orWhere('department2', $selectedDept)
                      ->orWhere('department3', $selectedDept);
                })
                ->orderByRaw($this->officeOrderSql())
                ->orderBy('name')
                ->get();

            if ($request->export === 'excel') {
                return $this->excelByDepartment($members, $selectedDept);
            }
        }

        // For "All Departments" view — each member may appear in multiple groups
        $allGrouped = null;
        if ($selectedDept === 'all') {
            $all = User::where('role', 'member')->orderBy('name')->get();
            $grouped = collect();
            foreach ($all as $m) {
                foreach (['department', 'department2', 'department3'] as $field) {
                    $dept = $m->$field;
                    if ($dept && $dept !== '') {
                        if (!$grouped->has($dept)) $grouped[$dept] = collect();
                        $grouped[$dept]->push($m);
                    }
                }
            }
            $allGrouped = $grouped->sortKeys();

            if ($request->export === 'excel') {
                // Flatten for export: include all assignments
                $flatMembers = collect();
                foreach ($allGrouped as $dept => $deptMembers) {
                    foreach ($deptMembers as $m) {
                        $flatMembers->push($m);
                    }
                }
                return $this->excelByDepartment($flatMembers, 'All Departments');
            }
        }

        return view('admin.reports.by-department', compact('departments', 'selectedDept', 'members', 'allGrouped'));
    }

    // ── Children by Parent Report ────────────────────────────────────────────
    public function childrenByParent(Request $request)
    {
        // Build list of all unique parent names from children table
        $parent1Names = Child::whereNotNull('parent1_name')->pluck('parent1_name');
        $parent2Names = Child::whereNotNull('parent2_name')->pluck('parent2_name');
        $allParentNames = $parent1Names->merge($parent2Names)->unique()->sort()->values();

        $selectedParent = $request->parent;
        $children = collect();

        if ($selectedParent) {
            // Find children where this person is parent1 OR parent2
            $children = Child::where('parent1_name', $selectedParent)
                ->orWhere('parent2_name', $selectedParent)
                ->orderBy('first_name')
                ->get();

            if ($request->export === 'excel') {
                return $this->excelChildrenByParent($children, $selectedParent);
            }
        }

        return view('admin.reports.children-by-parent', compact('allParentNames', 'selectedParent', 'children'));
    }

    // ── Excel Exports ────────────────────────────────────────────────────────

    private function excelMembership($members)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Full Membership');

        // Title
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Full Membership Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'Office', 'First Name', 'Last Name', 'Gender', 'Marital Status',
                    'Phone', 'Email', 'County', 'Department', 'Home Cell', 'Deacon/Deaconess', 'Joined'];
        foreach ($headers as $i => $h) {
            $col = $i + 1;
            $sheet->setCellValueByColumnAndRow($col, 4, $h);
        }
        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 5;
        foreach ($members as $i => $m) {
            $data = [
                $i + 1,
                $m->office ? ucfirst($m->office) : 'Member',
                $m->name,
                $m->last_name ?? '',
                $m->gender ? ucfirst($m->gender) : '',
                $m->marital_status ? ucfirst($m->marital_status) : '',
                $m->phone ?? '',
                $m->email,
                $m->county ?? '',
                $m->department ?? '',
                $m->home_cell ?? '',
                $m->deacon_name ?? '',
                $m->membership_date ? $m->membership_date->format('d/m/Y') : '',
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:M{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 13) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        return $this->streamExcel($spreadsheet, 'full_membership_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function excelLeaders($leaders)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Leaders');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Leaders Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'Office', 'First Name', 'Last Name', 'Gender', 'Phone', 'Email', 'Department'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $h);
        }
        $sheet->getStyle('A4:H4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 5;
        foreach ($leaders as $i => $m) {
            $data = [
                $i + 1,
                ucfirst($m->office),
                $m->name,
                $m->last_name ?? '',
                $m->gender ? ucfirst($m->gender) : '',
                $m->phone ?? '',
                $m->email,
                $m->department ?? '',
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:H{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 8) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        return $this->streamExcel($spreadsheet, 'leaders_report_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function excelMentorship($mentees, string $mentorName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Mentorship');

        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Mentorship Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Deacon / Deaconess: ' . $mentorName);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:J3');
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d M Y') . '   |   Members Assigned: ' . count($mentees));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'First Name', 'Last Name', 'Gender', 'Marital Status', 'Phone', 'Email', 'County', 'Department', 'Home Cell'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 5, $h);
        }
        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 6;
        foreach ($mentees as $i => $m) {
            $data = [
                $i + 1,
                $m->name,
                $m->last_name ?? '',
                $m->gender ? ucfirst($m->gender) : '',
                $m->marital_status ? ucfirst($m->marital_status) : '',
                $m->phone ?? '',
                $m->email,
                $m->county ?? '',
                $m->department ?? '',
                $m->home_cell ?? '',
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:J{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 10) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        return $this->streamExcel($spreadsheet, 'mentorship_' . str_replace(' ', '_', strtolower($mentorName)) . '_' . now()->format('Y-m-d') . '.xlsx');
    }

    private function excelChildrenByParent($children, string $parentName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Children by Parent');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Children by Parent Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Parent: ' . $parentName);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A3', 'Generated: ' . now()->format('d M Y') . '   |   Children: ' . count($children));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'Child Name', 'Gender', 'Date of Birth', 'Sunday School Class', 'Relationship'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 5, $h);
        }
        $sheet->getStyle('A5:F5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 6;
        foreach ($children as $i => $c) {
            $rel = ($c->parent1_name === $parentName) ? 'Parent 1' : 'Parent 2';
            $data = [
                $i + 1,
                $c->full_name,
                $c->gender ? ucfirst($c->gender) : '',
                $c->date_of_birth ? $c->date_of_birth->format('d/m/Y') : '',
                $c->sunday_school_class ?? '',
                $rel,
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:F{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 6) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $safe = preg_replace('/[^a-z0-9_]/i', '_', strtolower($parentName));
        return $this->streamExcel($spreadsheet, "children_{$safe}_" . now()->format('Y-m-d') . '.xlsx');
    }

    private function excelByDepartment($members, string $deptName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('By Department');

        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'Chrisco Upper Room FELLOWSHIP — Department Report: ' . $deptName);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => '0a1f44']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Generated: ' . now()->format('d M Y') . '   |   Members: ' . count($members));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['#', 'Full Name', 'Gender', 'Marital Status', 'Office', 'Phone', 'Email', 'County', 'Department 1', 'Department 2', 'Department 3', 'Home Cell', 'Deacon/Deaconess'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $h);
        }
        $sheet->getStyle('A4:M4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'c0392b']],
        ]);

        $row = 5;
        foreach ($members as $i => $m) {
            $data = [
                $i + 1,
                trim($m->name . ' ' . $m->middle_name . ' ' . $m->last_name),
                $m->gender ? ucfirst($m->gender) : '',
                $m->marital_status ? ucfirst($m->marital_status) : '',
                $m->office ? ucfirst($m->office) : 'Member',
                $m->phone ?? '',
                $m->email,
                $m->county ?? '',
                $m->department ?? '',
                $m->department2 ?? '',
                $m->department3 ?? '',
                $m->home_cell ?? '',
                $m->deacon_name ?? '',
            ];
            foreach ($data as $j => $val) {
                $sheet->setCellValueByColumnAndRow($j + 1, $row, $val);
            }
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:M{$row}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('f1f5f9');
            }
            $row++;
        }

        foreach (range(1, 13) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $safe = preg_replace('/[^a-z0-9_]/i', '_', strtolower($deptName));
        return $this->streamExcel($spreadsheet, "dept_{$safe}_" . now()->format('Y-m-d') . '.xlsx');
    }

    private function streamExcel(Spreadsheet $spreadsheet, string $filename)
    {
        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }
}

