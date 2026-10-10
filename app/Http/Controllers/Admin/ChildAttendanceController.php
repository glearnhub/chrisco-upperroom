<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildAttendance;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ChildAttendanceController extends Controller
{
    /** Attendance check-in page */
    public function scanner(Request $request)
    {
        $date    = $request->date ?? today()->toDateString();
        $class   = $request->class ?? '';
        $classes = Child::sundaySchoolClasses();

        // Who is already marked today
        $alreadyMarked = ChildAttendance::where('attendance_date', $date)
            ->when($class, fn($q) => $q->whereHas('child', fn($c) => $c->where('sunday_school_class', $class)))
            ->with('child')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.children.attendance.scanner', compact(
            'date', 'class', 'classes', 'alreadyMarked'
        ));
    }

    /** AJAX: search children by name for check-in */
    public function searchChild(Request $request)
    {
        $q     = trim($request->get('q', ''));
        $date  = $request->get('date', today()->toDateString());
        $class = $request->get('class', '');

        $query = Child::query();
        if ($q) {
            $query->where(function ($sq) use ($q) {
                $sq->where('first_name', 'like', "%{$q}%")
                   ->orWhere('last_name', 'like', "%{$q}%")
                   ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$q}%"]);
            });
        }
        if ($class) {
            $query->where('sunday_school_class', $class);
        }

        $children = $query->orderBy('first_name')->limit(10)->get(['id','first_name','last_name','sunday_school_class','photo']);

        $markedIds = ChildAttendance::where('attendance_date', $date)
            ->whereIn('child_id', $children->pluck('id'))
            ->pluck('child_id')
            ->toArray();

        return response()->json($children->map(fn($c) => [
            'id'         => $c->id,
            'name'       => $c->full_name,
            'class'      => $c->sunday_school_class ?? '—',
            'photo'      => $c->photo ? asset('storage/' . $c->photo) : null,
            'already_in' => in_array($c->id, $markedIds),
        ]));
    }

    /** AJAX: mark one child present */
    public function checkin(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:children,id',
            'date'     => 'required|date|before_or_equal:today|after:2020-01-01',
        ]);

        $exists = ChildAttendance::where('child_id', $request->child_id)
            ->where('attendance_date', $request->date)->exists();

        if ($exists) {
            return response()->json(['error' => 'already_in'], 409);
        }

        $record = ChildAttendance::create([
            'child_id'        => $request->child_id,
            'attendance_date' => $request->date,
            'method'          => 'manual',
            'confidence'      => 100,
            'marked_by'       => auth()->id(),
        ]);

        $child = Child::find($request->child_id);
        SystemLog::record('attendance', 'Children', "Marked {$child->full_name} present on {$request->date}");

        return response()->json([
            'success' => true,
            'id'      => $record->id,
            'name'    => $child->full_name,
            'class'   => $child->sunday_school_class ?? '—',
        ]);
    }

    /** AJAX: undo a check-in */
    public function undoCheckin(ChildAttendance $attendance)
    {
        if ($attendance->attendance_date->toDateString() < today()->toDateString() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Cannot undo attendance from a previous date.'], 403);
        }
        $attendance->delete();
        return response()->json(['success' => true]);
    }

    /** Attendance history / report */
    public function history(Request $request)
    {
        $date    = $request->date ?? today()->toDateString();
        $class   = $request->class;
        $classes = Child::sundaySchoolClasses();

        $records = ChildAttendance::with('child')
            ->where('attendance_date', $date)
            ->when($class, fn($q) => $q->whereHas('child', fn($c) => $c->where('sunday_school_class', $class)))
            ->orderBy('created_at')
            ->get();

        // Stats per class for given date
        $stats = ChildAttendance::selectRaw('COUNT(*) as present')
            ->where('attendance_date', $date)
            ->value('present');

        return view('admin.children.attendance.history', compact(
            'records', 'date', 'class', 'classes', 'stats'
        ));
    }

    /** All-dates report: one row per day attendance was taken */
    public function report(Request $request)
    {
        $class   = $request->class;
        $from    = $request->from;
        $to      = $request->to;
        $classes = Child::sundaySchoolClasses();

        // Distinct dates that have at least one record
        $dates = ChildAttendance::selectRaw('attendance_date, COUNT(*) as total')
            ->when($class, fn($q) => $q->whereHas('child', fn($c) => $c->where('sunday_school_class', $class)))
            ->when($from,  fn($q) => $q->where('attendance_date', '>=', $from))
            ->when($to,    fn($q) => $q->where('attendance_date', '<=', $to))
            ->groupBy('attendance_date')
            ->orderByDesc('attendance_date')
            ->get();

        // Per-class breakdown for each date
        $breakdown = ChildAttendance::selectRaw('attendance_date, children.sunday_school_class, COUNT(*) as cnt')
            ->join('children', 'children.id', '=', 'child_attendances.child_id')
            ->when($class, fn($q) => $q->where('children.sunday_school_class', $class))
            ->when($from,  fn($q) => $q->where('attendance_date', '>=', $from))
            ->when($to,    fn($q) => $q->where('attendance_date', '<=', $to))
            ->groupBy('attendance_date', 'children.sunday_school_class')
            ->get()
            ->groupBy('attendance_date');

        return view('admin.children.attendance.report', compact(
            'dates', 'breakdown', 'class', 'from', 'to', 'classes'
        ));
    }

    /** Remove one attendance record */
    public function remove(ChildAttendance $attendance)
    {
        if ($attendance->attendance_date->toDateString() < today()->toDateString() && !auth()->user()->isSuperAdmin()) {
            return back()->with('error', 'Cannot remove attendance from a previous date.');
        }
        $attendance->delete();
        return back()->with('success', 'Attendance record removed.');
    }

}
