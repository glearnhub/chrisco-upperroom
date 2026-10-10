<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $query = Donation::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('giving_type')) {
            $query->where('giving_type', $request->giving_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('donor_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('transaction_code', 'like', "%{$search}%");
            });
        }

        $donations = $query->paginate(20)->withQueryString();

        $totalCompleted = Donation::where('status', 'confirmed')->sum('amount');

        return view('admin.donations.index', compact('donations', 'totalCompleted'));
    }

    public function show(Donation $donation)
    {
        $donation->load('user');
        return view('admin.donations.show', compact('donation'));
    }

    public function updateStatus(Request $request, Donation $donation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,rejected',
        ]);

        $donation->update(['status' => $validated['status']]);

        return back()->with('success', 'Donation status updated successfully.');
    }
}
