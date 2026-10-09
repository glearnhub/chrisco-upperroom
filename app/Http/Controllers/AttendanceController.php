<?php

namespace App\Http\Controllers;

use App\Models\ServiceSession;
use App\Models\ServiceAttendance;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    // GET /attend?door=A
    public function show(Request $request)
    {
        $door    = strtoupper($request->query('door', 'A'));
        $door    = in_array($door, ['A', 'B', 'C']) ? $door : 'A';
        $session = ServiceSession::currentOpen();

        return view('attendance.checkin', compact('door', 'session'));
    }

    // POST /attend/search
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|min:3|max:100']);
        $q = trim($request->q);

        $session = ServiceSession::currentOpen();
        if (!$session) {
            return response()->json(['error' => 'no_session'], 422);
        }

        $memberList = User::where('role', 'member')
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->whereRaw("CONCAT(name, ' ', COALESCE(middle_name,''), ' ', COALESCE(last_name,'')) LIKE ?", ["%{$q}%"])
                      ->orWhere('phone', 'like', "%{$q}%")
                      ->orWhere('email',  'like', "%{$q}%");
            })
            ->select('id', 'name', 'middle_name', 'last_name', 'phone', 'profile_photo', 'department', 'office')
            ->limit(8)
            ->get();

        $memberIds   = $memberList->pluck('id');
        $attendances = ServiceAttendance::where('session_id', $session->id)
            ->whereIn('user_id', $memberIds)
            ->get()
            ->keyBy('user_id');

        $members = $memberList->map(function ($m) use ($attendances) {
            $attendance = $attendances->get($m->id);
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

        return response()->json(['members' => $members, 'session_id' => $session->id]);
    }

    // POST /attend/checkin
    public function checkin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'door'    => 'required|in:A,B,C',
        ]);

        $session = ServiceSession::currentOpen();
        if (!$session) {
            return response()->json(['error' => 'No service session is currently open.'], 422);
        }

        $user = User::where('id', $request->user_id)->where('role', 'member')->where('is_active', true)->first();
        if (!$user) {
            return response()->json(['error' => 'Member not found.'], 422);
        }

        // Prevent duplicate check-in
        $exists = ServiceAttendance::where('session_id', $session->id)
            ->where('user_id', $user->id)->exists();

        if ($exists) {
            return response()->json(['error' => 'already_checked_in', 'name' => $user->full_name], 409);
        }

        try {
            ServiceAttendance::create([
                'session_id'    => $session->id,
                'user_id'       => $user->id,
                'door'          => $request->door,
                'method'        => 'self',
                'checked_in_at' => now(),
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23000')) {
                return response()->json(['error' => 'already_checked_in', 'name' => $user->full_name], 409);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'name'    => $user->full_name,
            'count'   => $session->fresh()->attendanceCount(),
        ]);
    }
}
