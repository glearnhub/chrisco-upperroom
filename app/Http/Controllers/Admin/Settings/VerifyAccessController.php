<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class VerifyAccessController extends Controller
{
    public function index()
    {
        return view('admin.settings.verify-access');
    }

    public function update(Request $request)
    {
        $request->validate([
            'verify_access_start' => 'nullable|date',
            'verify_access_end'   => 'nullable|date|after_or_equal:verify_access_start',
            'verify_access_enabled' => 'boolean',
        ]);

        Setting::set('verify_access_enabled', $request->boolean('verify_access_enabled') ? '1' : '0', 'verify');
        Setting::set('verify_access_start', $request->verify_access_start ?? '', 'verify');
        Setting::set('verify_access_end',   $request->verify_access_end   ?? '', 'verify');

        return back()->with('success', 'Verify access window updated.');
    }
}
