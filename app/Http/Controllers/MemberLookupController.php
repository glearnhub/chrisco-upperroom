<?php

namespace App\Http\Controllers;

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
        $request->validate([
            'email' => 'required|email',
        ]);

        $member = User::where('email', $request->email)->first();

        return view('member-lookup.index', compact('member'))->with('searched', true);
    }
}
