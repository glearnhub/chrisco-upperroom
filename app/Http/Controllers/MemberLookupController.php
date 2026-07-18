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

        $member = User::where('email', $request->email)->first();

        $children = null;
        if ($member) {
            $children = Child::where('parent1_id', $member->id)
                ->orWhere('parent2_id', $member->id)
                ->orderBy('first_name')
                ->get();
        }

        return view('member-lookup.index', compact('member', 'children'))->with('searched', true);
    }

    public function requestCorrection(Request $request)
    {
        $request->validate([
            'email'   => 'required|email',
            'message' => 'required|string|min:10|max:1000',
        ]);

        $member = User::where('email', $request->email)->firstOrFail();

        CorrectionRequest::create([
            'user_id' => $member->id,
            'message' => $request->message,
        ]);

        return back()->with('correction_sent', true)->with('lookup_email', $request->email);
    }
}
