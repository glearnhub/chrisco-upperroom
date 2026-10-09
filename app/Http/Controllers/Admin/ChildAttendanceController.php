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
        $attendance->delete();
        return response()->json(['success' => true]);
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
            'date'          => 'required|date|before_or_equal:today|after:2020-01-01',
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
            // Only accept known image MIME prefixes
            if (!preg_match('/^data:image\/(jpeg|png|webp);base64,/', $request->photo)) {
                return response()->json(['error' => 'Invalid image format.'], 422);
            }

            $data    = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo);
            $decoded = base64_decode($data, strict: true);

            if ($decoded === false) {
                return response()->json(['error' => 'Invalid base64 data.'], 422);
            }

            // Reject payloads over 2 MB
            if (strlen($decoded) > 2 * 1024 * 1024) {
                return response()->json(['error' => 'Image exceeds 2 MB limit.'], 422);
            }

            // Verify the decoded bytes are actually an image
            if (!@getimagesizefromstring($decoded)) {
                return response()->json(['error' => 'Uploaded file is not a valid image.'], 422);
            }

            $filename = 'children/' . $child->id . '_' . time() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $decoded);

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
