<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildAttendance;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class ChildAttendanceController extends Controller
{
    /** Attendance scanner page */
    public function scanner(Request $request)
    {
        $date     = $request->date ?? today()->toDateString();
        $class    = $request->class;
        $classes  = Child::sundaySchoolClasses();

        // Children with photos/descriptors for the chosen class
        $query = Child::whereNotNull('face_descriptor');
        if ($class) $query->where('sunday_school_class', $class);
        $enrolled = $query->count();

        // Who is already marked today
        $alreadyMarked = ChildAttendance::where('attendance_date', $date)
            ->when($class, fn($q) => $q->whereHas('child', fn($c) => $c->where('sunday_school_class', $class)))
            ->with('child')
            ->get();

        return view('admin.children.attendance.scanner', compact(
            'date', 'class', 'classes', 'enrolled', 'alreadyMarked'
        ));
    }

    /** Return all face descriptors for a class (used by JS) */
    public function descriptors(Request $request)
    {
        $query = Child::whereNotNull('face_descriptor')->whereNotNull('photo');
        if ($request->class) {
            $query->where('sunday_school_class', $request->class);
        }

        $data = $query->get(['id', 'first_name', 'last_name', 'photo', 'face_descriptor'])
            ->map(fn($c) => [
                'id'          => $c->id,
                'name'        => $c->full_name,
                'photo'       => $c->photo_url,
                'descriptor'  => $c->face_descriptor,
            ]);

        return response()->json($data);
    }

    /** Save attendance records sent from JS after face matching */
    public function saveAttendance(Request $request)
    {
        $request->validate([
            'date'          => 'required|date',
            'matches'       => 'required|array',
            'matches.*.id'  => 'required|exists:children,id',
            'matches.*.confidence' => 'required|numeric|min:0|max:100',
            'matches.*.method'     => 'required|in:face,manual',
        ]);

        $saved = 0;
        foreach ($request->matches as $match) {
            ChildAttendance::updateOrCreate(
                ['child_id' => $match['id'], 'attendance_date' => $request->date],
                [
                    'method'     => $match['method'],
                    'confidence' => $match['confidence'],
                    'marked_by'  => auth()->id(),
                ]
            );
            $saved++;
        }

        SystemLog::record('attendance', 'Children',
            "Marked {$saved} child(ren) present on {$request->date}");

        return response()->json(['saved' => $saved]);
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
        $attendance->delete();
        return back()->with('success', 'Attendance record removed.');
    }

    /** Save face descriptor sent from child edit/create page */
    public function saveDescriptor(Request $request, Child $child)
    {
        $request->validate([
            'descriptor' => 'required|array|size:128',
            'photo'      => 'nullable|string', // base64 data URL
        ]);

        // Save base64 photo to disk if provided
        if ($request->photo) {
            $data     = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo);
            $filename = 'children/' . $child->id . '_' . time() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, base64_decode($data));

            // Delete old photo
            if ($child->photo && $child->photo !== $filename) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($child->photo);
            }

            $child->photo = $filename;
        }

        $child->face_descriptor = $request->descriptor;
        $child->save();

        return response()->json(['success' => true, 'photo_url' => $child->photo_url]);
    }
}
