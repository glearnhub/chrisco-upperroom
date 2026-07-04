<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorrectionRequest;
use Illuminate\Http\Request;

class CorrectionRequestController extends Controller
{
    public function index()
    {
        $requests = CorrectionRequest::with('user')
            ->orderByRaw("FIELD(status, 'pending', 'reviewed', 'resolved')")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.corrections.index', compact('requests'));
    }

    public function update(Request $request, CorrectionRequest $correction)
    {
        $request->validate([
            'status'      => 'required|in:pending,reviewed,resolved',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $correction->update($request->only('status', 'admin_notes'));

        return back()->with('success', 'Request updated.');
    }
}
