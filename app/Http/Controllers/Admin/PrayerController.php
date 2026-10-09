<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use App\Mail\PrayerAssignedMail;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PrayerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = PrayerRequest::with(['user', 'assignedLeader'])->orderBy('created_at', 'desc');

        if (! $user->isSuperAdmin()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned') && $user->isSuperAdmin()) {
            if ($request->assigned === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned);
            }
        }

        $prayers = $query->paginate(20)->withQueryString();

        $leaderOffices = ['presbyter', 'pastor', 'elder', 'deacon', 'deaconess'];
        $leaders = $user->isSuperAdmin()
            ? User::where('role', 'admin')
                ->where('is_active', true)
                ->whereIn('office', $leaderOffices)
                ->orderBy('name')
                ->get(['id', 'name', 'last_name', 'office', 'phone'])
            : collect();

        return view('admin.prayers.index', compact('prayers', 'leaders'));
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'prayer_ids'  => 'required|array|min:1',
            'prayer_ids.*' => 'exists:prayer_requests,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $leader  = User::findOrFail($request->assigned_to);
        $prayers = PrayerRequest::whereIn('id', $request->prayer_ids)->get();

        PrayerRequest::whereIn('id', $prayers->pluck('id'))->update(['assigned_to' => $leader->id]);

        if ($leader->email) {
            try {
                Mail::to($leader->email)->send(new PrayerAssignedMail($prayers, $leader));
            } catch (\Exception $e) {
                // Mail failure does not block the assignment
            }
        }

        SystemLog::record('assign', 'prayers',
            "Bulk assigned {$prayers->count()} prayer(s) to {$leader->name}");

        $waPhone = preg_replace('/\D/', '', $leader->phone ?? '');
        if ($waPhone && str_starts_with($waPhone, '0')) {
            $waPhone = '254' . substr($waPhone, 1);
        }
        $waUrl = $waPhone
            ? 'https://wa.me/' . $waPhone . '?text=' . rawurlencode(
                "Hello " . trim($leader->name . ' ' . $leader->last_name) . ",\n\n"
                . "You have been assigned {$prayers->count()} prayer request(s) on the Chrisco Upper Room Fellowship system. Please check your email for full details and remember to pray and follow up on each one.\n\nGod bless you!")
            : null;

        return back()
            ->with('success', $prayers->count() . ' prayer request(s) assigned to ' . trim($leader->name . ' ' . $leader->last_name) . ' and notified by email.')
            ->with('whatsapp_url', $waUrl)
            ->with('whatsapp_name', trim($leader->name . ' ' . $leader->last_name));
    }

    public function assign(Request $request, PrayerRequest $prayer)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $leaderId = $request->assigned_to ?: null;
        $prayer->update(['assigned_to' => $leaderId]);

        if ($leaderId) {
            $leader = User::find($leaderId);
            if ($leader && $leader->email) {
                try {
                    Mail::to($leader->email)->send(new PrayerAssignedMail(
                        collect([$prayer]), $leader
                    ));
                } catch (\Exception $e) {
                    //
                }
            }
            SystemLog::record('assign', 'prayers', "Prayer #{$prayer->id} assigned to {$leader?->name}");
        } else {
            SystemLog::record('assign', 'prayers', "Prayer #{$prayer->id} unassigned");
        }

        return back()->with('success', $leaderId
            ? 'Prayer request assigned and leader notified by email.'
            : 'Prayer request unassigned.');
    }

    public function updateStatus(Request $request, PrayerRequest $prayer)
    {
        $user = auth()->user();

        if (! $user->isSuperAdmin() && $prayer->assigned_to !== $user->id) {
            abort(403);
        }

        $allowedStatuses = $user->isSuperAdmin()
            ? ['ongoing', 'pending', 'prayed', 'answered']
            : ['ongoing', 'pending', 'prayed'];

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowedStatuses)],
        ]);

        $prayer->update(['status' => $validated['status']]);

        SystemLog::record('update', 'prayers', "Prayer #{$prayer->id} status → {$validated['status']}");

        return back()->with('success', 'Prayer request status updated.');
    }
}
