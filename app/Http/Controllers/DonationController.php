<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index()
    {
        return view('give.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name'       => 'required|string|max:255',
            'phone'            => 'required|string|max:20',
            'amount'           => 'required|numeric|min:1|max:999999',
            'giving_type'      => 'required|in:tithe,offering,thanksgiving,building_fund,missions,other',
            'payment_method'   => 'required|string|max:100',
            'transaction_code' => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        $donation = Donation::create([
            'user_id'          => auth()->id(),
            'donor_name'       => $validated['donor_name'],
            'phone'            => $validated['phone'],
            'amount'           => $validated['amount'],
            'giving_type'      => $validated['giving_type'],
            'payment_method'   => $validated['payment_method'],
            'transaction_code' => $validated['transaction_code'] ?? null,
            'status'           => 'pending',
            'notes'            => $validated['notes'] ?? null,
        ]);

        return redirect()->route('give')->with('success', 'Thank you for your generous giving! Your donation of KES ' . number_format($validated['amount'], 2) . ' has been recorded.');
    }
}
