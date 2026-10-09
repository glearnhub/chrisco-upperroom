<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\CorrectionRequest;
use App\Models\User;
use Illuminate\Http\Request;

class MemberLookupController extends Controller
{
    public function index()
    {
        return view('member-lookup.index');
    }

    public function lookup(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $member = User::where('email', $request->email)
            ->select('id', 'name', 'middle_name', 'last_name', 'email', 'profile_photo', 'department', 'office')
            ->first();

        $children = null;
        if ($member) {
            $children = Child::where('parent1_id', $member->id)
                ->orWhere('parent2_id', $member->id)
                ->orderBy('first_name')
                ->select('id', 'first_name', 'last_name', 'date_of_birth', 'class_group')
                ->get();

            // Store member ID in session for correction request to prevent IDOR
            session(['lookup_member_id' => $member->id]);
        }

        return view('member-lookup.index', compact('member', 'children'))->with('searched', true);
    }

    public function requestCorrection(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:10|max:1000',
        ]);

        $memberId = session('lookup_member_id');

        if ($memberId) {
            CorrectionRequest::create([
                'user_id' => $memberId,
                'message' => $request->message,
            ]);
            session()->forget('lookup_member_id');
        }

        return back()->with('correction_sent', true);
    }
}
