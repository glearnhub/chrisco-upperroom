<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerRequest;
use Illuminate\Http\Request;

class PrayerController extends Controller
{
    public function index(Request $request)
    {
        $query = PrayerRequest::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prayers = $query->paginate(20)->withQueryString();

        return view('admin.prayers.index', compact('prayers'));
    }

    public function updateStatus(Request $request, PrayerRequest $prayer)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,prayed,answered',
        ]);

        $prayer->update(['status' => $validated['status']]);

        return back()->with('success', 'Prayer request status updated.');
    }
}
