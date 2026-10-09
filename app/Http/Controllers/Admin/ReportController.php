<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Child;
use App\Models\AttendanceFollowup;
use App\Models\ServiceSession;
use App\Models\ServiceAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    // ── Committed Members ───────────────────────────────────────────────────
    public function committed(Request $request)
    {
        $members = User::where('role', 'member')->where('is_committed_member', true)->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Committed Members');
        return view('admin.reports.filter', compact('members'), ['title' => 'Committed Members', 'subtitle' => 'Members who have completed the commitment class']);
    }

    // ── In Commitment Class ─────────────────────────────────────────────────
    public function inCommitment(Request $request)
    {
        $members = User::where('role', 'member')->where('in_commitment_class', true)->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'In Commitment Class');
        return view('admin.reports.filter', compact('members'), ['title' => 'In Commitment Class', 'subtitle' => 'Members currently attending commitment class']);
    }

    // ── Young Converts ──────────────────────────────────────────────────────
    public function youngConverts(Request $request)
    {
        $members = User::where('role', 'member')->where('is_committed_member', false)->where('in_commitment_class', false)->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Young Converts');
        return view('admin.reports.filter', compact('members'), ['title' => 'Young Converts', 'subtitle' => 'Not committed & not in commitment class']);
    }

    // ── Not Baptised ────────────────────────────────────────────────────────
    public function notBaptised(Request $request)
    {
        $members = User::where('role', 'member')->where('is_born_again', true)->where('is_baptized', false)->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Not Baptised');
        return view('admin.reports.filter', compact('members'), ['title' => 'Not Baptised', 'subtitle' => 'Born again but not yet baptised (full immersion)']);
    }

    // ── Married ─────────────────────────────────────────────────────────────
    public function married(Request $request)
    {
        $members = User::where('role', 'member')->where('marital_status', 'married')->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Married Members');
        return view('admin.reports.filter', compact('members'), ['title' => 'Married Members', 'subtitle' => 'All married members']);
    }

    // ── Pearls Fellowship ───────────────────────────────────────────────────
    public function pearls(Request $request)
    {
        $members = User::where('role', 'member')->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 30')
            ->whereNotIn('marital_status', ['married'])->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Pearls Fellowship');
        return view('admin.reports.filter', compact('members'), ['title' => 'Pearls Fellowship', 'subtitle' => 'Above 30 years, not married']);
    }

    // ── Singles / Youths ────────────────────────────────────────────────────
    public function singlesYouths(Request $request)
    {
        $members = User::where('role', 'member')->whereNotNull('date_of_birth')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 18')
            ->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 30')
            ->whereNotIn('marital_status', ['married'])->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Singles / Youths');
        return view('admin.reports.filter', compact('members'), ['title' => 'Singles / Youths', 'subtitle' => 'Above 18, not married']);
    }

    // ── Transferred In ──────────────────────────────────────────────────────
    public function transferredIn(Request $request)
    {
        $members = User::where('role', 'member')->where('transfer_type', 'in')
            ->orderByRaw($this->officeOrderSql())->orderBy('name')->get();
        if ($request->boolean('export')) return $this->filterExport($members, 'Transferred In');
        return view('admin.reports.filter', compact('members'), [
            'title'    => 'Transferred In',
            'subtitle' => 'Members who joined from another Chrisco Church',
        ]);
    }

    // ── Shared Excel export for simple filter reports ───────────────────────
    private function filterExport($members, string $title)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle(substr($title, 0, 31));

        $headers = ['#', 'Name', 'Gender', 'Phone', 'Email', 'Department', 'Office', 'Membership Date'];
        foreach ($headers as $col => $h) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $h);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF0A1F44');
            $sheet->getStyle($cell)->getFont()->getColor()->setARGB('FFFFFFFF');
        }

        foreach ($members as $i => $m) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", trim("{$m->name} {$m->middle_name} {$m->last_name}"));
            $sheet->setCellValue("C{$row}", ucfirst($m->gender ?? ''));
            $sheet->setCellValue("D{$row}", $m->phone ?? '');
            $sheet->setCellValue("E{$row}", $m->email ?? '');
            $sheet->setCellValue("F{$row}", $m->department ?? '');
            $sheet->setCellValue("G{$row}", $m->office ?? '');
            $sheet->setCellValue("H{$row}", $m->membership_date ?? '');
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $safe = preg_replace('/[^a-z0-9]+/', '_', strtolower($title));
        return $this->streamExcel($spreadsheet, "{$safe}_" . now()->format('Y-m-d') . '.xlsx');
    }

    // ── Transferred Out ─────────────────────────────────────────────────────
    public function transferredOut()
    {
        // Sourced from follow-up records where reason = 'transferred' (most recent per member)
        $userIds = AttendanceFollowup::where('reason', 'transferred')
            ->orderByDesc('year')->orderByDesc('month')
            ->get()
            ->groupBy('user_id')
            ->keys();

        $members = User::whereIn('id', $userIds)
            ->orderByRaw($this->officeOrderSql())
            ->orderBy('name')
            ->get();

        return view('admin.reports.filter', compact('members'), [
            'title'    => 'Transferred Out',
            'subtitle' => 'Members who transferred to another Chrisco Church',
        ]);
    }

    // ── Active Members ──────────────────────────────────────────────────────
    // Active = attended all but at most 1 of the last 4 closed Sunday sessions
    public function activeMembers()
    {
        $last4 = ServiceSession::where('status', 'closed')
            ->orderByDesc('service_date')
            ->limit(4)
            ->pluck('id');

        if ($last4->isEmpty()) {
            $members = collect();
            return view('admin.reports.filter', compact('members'), [
                'title'    => 'Active Members',
                'subtitle' => 'No closed Sunday sessions on record yet',
            ]);
        }

        // Threshold scales with available sessions: require all but at most 1 absence
        $threshold = max(1, $last4->count() - 1);

        $activeIds = ServiceAttendance::whereIn('session_id', $last4)
            ->select('user_id', DB::raw('COUNT(*) as times'))
            ->groupBy('user_id')
            ->having('times', '>=', $threshold)
            ->pluck('user_id');

        $members = User::where('role', 'member')
            ->whereIn('id', $activeIds)
            ->orderByRaw($this->officeOrderSql())
            ->orderBy('name')
            ->get();

        return view('admin.reports.filter', compact('members'), [
            'title'    => 'Active Members',
            'subtitle' => 'Attended ' . $threshold . ' or more of the last ' . $last4->count() . ' Sunday services',
        ]);
    }

    // ── Inactive Members ────────────────────────────────────────────────────
    // Inactive = attended 0 of the last 4 closed Sunday sessions
    public function inactiveMembers()
    {
        $last4 = ServiceSession::where('status', 'closed')
            ->orderByDesc('service_date')
            ->limit(4)
            ->pluck('id');

        if ($last4->isEmpty()) {
            $members = collect();
            return view('admin.reports.filter', compact('members'), [
                'title'    => 'Inactive Members',
                'subtitle' => 'No closed Sunday sessions on record yet',
            ]);
        }

        $attendedIds = ServiceAttendance::whereIn('session_id', $last4)
            ->distinct()
            ->pluck('user_id');

        $members = User::where('role', 'member')
            ->whereNotIn('id', $attendedIds)
            ->orderByRaw($this->officeOrderSql())
            ->orderBy('name')
            ->get();

        return view('admin.reports.filter', compact('members'), [
            'title'    => 'Inactive Members',
            'subtitle' => 'Have not attended any of the last ' . $last4->count() . ' Sunday services',
        ]);
    }
}

