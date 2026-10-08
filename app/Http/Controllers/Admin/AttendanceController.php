<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceSession;
use App\Models\ServiceAttendance;
use App\Models\AttendanceFollowup;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // GET /admin/attendance
    public function index()
    {
        $openSession  = ServiceSession::currentOpen();
        $recentSessions = ServiceSession::with(['openedBy', 'closedBy'])
            ->orderByDesc('service_date')
            ->orderByDesc('opened_at')
            ->limit(10)
            ->get();

        return view('admin.attendance.index', compact('openSession', 'recentSessions'));
    }

    // POST /admin/attendance/open
    public function open(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date',
            'service_type' => 'required|in:sunday_morning,sunday_afternoon,special',
            'label'        => 'nullable|string|max:100',
        ]);

        // Only one session open at a time
        if (ServiceSession::currentOpen()) {
            return back()->with('error', 'A session is already open. Close it before opening a new one.');
        }

        // Check for a session with the same date + type (unique constraint)
        $existing = ServiceSession::where('service_date', $request->service_date)
            ->where('service_type', $request->service_type)
            ->first();

        if ($existing) {
            if ($existing->status === 'closed') {
                // Re-open the closed session instead of failing
                $existing->update(['status' => 'open', 'opened_at' => now(), 'opened_by' => auth()->id(), 'closed_at' => null, 'closed_by' => null]);
                return back()->with('success', 'Previous session re-opened. Members can now check in.');
            }
            return back()->with('error', 'A session for this date and service type already exists.');
        }

        ServiceSession::create([
            'service_date' => $request->service_date,
            'service_type' => $request->service_type,
            'label'        => $request->label,
            'status'       => 'open',
            'opened_at'    => now(),
            'opened_by'    => auth()->id(),
        ]);

        return back()->with('success', 'Session opened. Members can now check in.');
    }

    // POST /admin/attendance/close/{session}
    public function close(ServiceSession $session)
    {
        if (!$session->isOpen()) {
            return back()->with('error', 'This session is not open.');
        }

        $session->update([
            'status'     => 'closed',
            'closed_at'  => now(),
            'closed_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Session closed. Total attendance: ' . $session->attendanceCount());
    }

    // GET /admin/attendance/session/{session} — live check-in panel for usher
    public function session(ServiceSession $session)
    {
        $attendances = $session->attendances()
            ->with('member')
            ->orderByDesc('checked_in_at')
            ->get();

        $totalMembers = \App\Models\User::where('role', 'member')->where('is_active', true)->count();

        return view('admin.attendance.session', compact('session', 'attendances', 'totalMembers'));
    }

    // GET /admin/attendance/session/{session}/search (AJAX)
    public function searchMember(Request $request, ServiceSession $session)
    {
        $request->validate(['q' => 'required|string|min:2']);
        $q = trim($request->q);

        $members = User::where('role', 'member')
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->whereRaw("CONCAT(name, ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$q}%"])
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->select('id', 'name', 'middle_name', 'last_name', 'phone', 'profile_photo', 'department', 'office')
            ->limit(8)
            ->get()
            ->map(function ($m) use ($session) {
                $attendance = ServiceAttendance::where('session_id', $session->id)
                    ->where('user_id', $m->id)->first();
                return [
                    'id'           => $m->id,
                    'name'         => $m->full_name,
                    'phone'        => $m->phone,
                    'department'   => trim(implode(' · ', array_filter([$m->office, $m->department]))),
                    'photo'        => $m->profile_photo ? asset('storage/' . $m->profile_photo) : null,
                    'already_in'   => (bool) $attendance,
                    'checked_in_at'=> $attendance ? $attendance->checked_in_at->format('H:i') : null,
                ];
            });

        return response()->json([
            'members'      => $members,
            'total_in'     => $session->attendanceCount(),
        ]);
    }

    // POST /admin/attendance/session/{session}/checkin (AJAX — usher check-in)
    public function ushercheckin(Request $request, ServiceSession $session)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        if (!$session->isOpen()) {
            return response()->json(['error' => 'Session is closed.'], 422);
        }

        $exists = ServiceAttendance::where('session_id', $session->id)
            ->where('user_id', $request->user_id)->exists();

        if ($exists) {
            return response()->json(['error' => 'already_in'], 409);
        }

        $attendance = ServiceAttendance::create([
            'session_id'    => $session->id,
            'user_id'       => $request->user_id,
            'door'          => 'usher',
            'method'        => 'usher',
            'checked_in_at' => now(),
        ]);

        $member = User::find($request->user_id);

        return response()->json([
            'success'  => true,
            'name'     => $member->full_name,
            'total_in' => $session->fresh()->attendanceCount(),
            'entry'    => [
                'id'           => $attendance->id,
                'name'         => $member->full_name,
                'phone'        => $member->phone,
                'checked_in_at'=> $attendance->checked_in_at->format('H:i'),
            ],
        ]);
    }

    // DELETE /admin/attendance/checkin/{attendance} (usher undo)
    public function undoCheckin(ServiceAttendance $attendance)
    {
        if (!$attendance->session->isOpen()) {
            return response()->json(['error' => 'Cannot undo after session is closed.'], 422);
        }

        $sessionId = $attendance->session_id;
        $attendance->delete();

        return response()->json([
            'success'  => true,
            'total_in' => ServiceSession::find($sessionId)->attendanceCount(),
        ]);
    }

    // GET /admin/attendance/report — monthly member activity report
    public function report(Request $request)
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = (int) $request->get('month', now()->month);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        // All Sundays in this month
        $sundays = [];
        $cur = $startOfMonth->copy();
        while ($cur->lte($endOfMonth)) {
            if ($cur->isSunday()) {
                $sundays[] = $cur->toDateString();
            }
            $cur->addDay();
        }
        $totalSundays = count($sundays);

        // Sessions held on those Sundays (sunday_morning / sunday_afternoon)
        $sessions = ServiceSession::whereIn('service_date', $sundays)
            ->whereIn('service_type', ['sunday_morning', 'sunday_afternoon'])
            ->orderBy('service_date')
            ->orderBy('service_type')
            ->withCount('attendances')
            ->get();

        $sessionIds  = $sessions->pluck('id')->toArray();

        // Unique Sundays that actually had a session
        $sundaysWithSession = $sessions->pluck('service_date')
            ->map(fn($d) => $d instanceof \Carbon\Carbon ? $d->toDateString() : $d)
            ->unique()->values()->toArray();

        // All attendances for those sessions
        $allAttendances = ServiceAttendance::whereIn('session_id', $sessionIds)
            ->get(['session_id', 'user_id']);

        // Map session_id → service_date string
        $sessionDateMap = [];
        foreach ($sessions as $s) {
            $sessionDateMap[$s->id] = $s->service_date instanceof \Carbon\Carbon
                ? $s->service_date->toDateString()
                : $s->service_date;
        }

        // Per member: which distinct Sundays did they attend?
        $memberSundayMap = []; // user_id => [date => true]
        foreach ($allAttendances as $att) {
            $date = $sessionDateMap[$att->session_id] ?? null;
            if ($date) {
                $memberSundayMap[$att->user_id][$date] = true;
            }
        }

        // All active members
        $members = User::where('role', 'member')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'middle_name', 'last_name', 'phone', 'department', 'office']);

        $activeMembers   = [];
        $inactiveMembers = [];
        $irregularMembers = [];

        foreach ($members as $member) {
            $attended = count($memberSundayMap[$member->id] ?? []);
            $missed   = $totalSundays - $attended;

            $member->sundays_attended = $attended;
            $member->sundays_missed   = $missed;
            // Which specific sundays they attended
            $member->attended_dates = array_keys($memberSundayMap[$member->id] ?? []);

            if ($attended >= 3) {
                $activeMembers[] = $member;
            } elseif ($missed >= 3) {
                $inactiveMembers[] = $member;
            } else {
                $irregularMembers[] = $member;
            }
        }

        // Sort inactive by most missed first
        usort($inactiveMembers, fn($a, $b) => $b->sundays_missed <=> $a->sundays_missed);

        $monthName = $startOfMonth->format('F Y');

        // Load existing follow-ups for this month
        $memberIds = $members->pluck('id')->toArray();
        $followups = AttendanceFollowup::where('year', $year)
            ->where('month', $month)
            ->whereIn('user_id', $memberIds)
            ->with('recordedBy')
            ->get()
            ->keyBy('user_id');

        $reasonLabels = AttendanceFollowup::$reasonLabels;

        return view('admin.attendance.report', compact(
            'year', 'month', 'monthName', 'sundays', 'totalSundays',
            'sessions', 'sundaysWithSession',
            'members', 'activeMembers', 'inactiveMembers', 'irregularMembers',
            'startOfMonth', 'followups', 'reasonLabels'
        ));
    }

    // POST /admin/attendance/followup — save/update a follow-up reason for an inactive member
    public function saveFollowup(Request $request)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'year'           => 'required|integer|min:2020|max:2100',
            'month'          => 'required|integer|min:1|max:12',
            'reason'         => 'required|in:transferred,left_church,unwell,job_related,mission_field,other',
            'transferred_to' => 'nullable|string|max:200',
            'notes'          => 'nullable|string|max:500',
        ]);

        $followup = AttendanceFollowup::updateOrCreate(
            ['user_id' => $data['user_id'], 'year' => $data['year'], 'month' => $data['month']],
            array_merge($data, ['recorded_by' => auth()->id()])
        );

        $followup->load('recordedBy');

        return response()->json([
            'success'       => true,
            'reason_label'  => AttendanceFollowup::$reasonLabels[$followup->reason] ?? $followup->reason,
            'transferred_to'=> $followup->transferred_to,
            'notes'         => $followup->notes,
            'recorded_by'   => $followup->recordedBy?->full_name ?? 'Unknown',
        ]);
    }

    // DELETE /admin/attendance/followup — clear a follow-up record
    public function deleteFollowup(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year'    => 'required|integer',
            'month'   => 'required|integer',
        ]);

        AttendanceFollowup::where('user_id', $request->user_id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->delete();

        return response()->json(['success' => true]);
    }

    // GET /admin/attendance/qr-codes — static QR download page
    public function qrCodes()
    {
        return view('admin.attendance.qr-codes');
    }
}
