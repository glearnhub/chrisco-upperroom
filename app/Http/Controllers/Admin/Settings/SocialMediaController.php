<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public function index()
    {
        return view('admin.settings.social');
    }

    public function update(Request $request)
    {
        $request->validate([
            'facebook'  => ['nullable', 'url', 'max:255', 'regex:/^https:\/\/(www\.)?facebook\.com\//i'],
            'instagram' => ['nullable', 'url', 'max:255', 'regex:/^https:\/\/(www\.)?instagram\.com\//i'],
            'youtube'   => ['nullable', 'url', 'max:255', 'regex:/^https:\/\/(www\.)?youtube\.com\//i'],
            'tiktok'    => ['nullable', 'url', 'max:255', 'regex:/^https:\/\/(www\.)?tiktok\.com\//i'],
        ]);

        foreach (['facebook', 'instagram', 'youtube', 'tiktok'] as $key) {
            Setting::set("social.{$key}", $request->input($key), 'social');
        }

        SystemLog::record('update', 'Settings', 'Social media links updated.');

        return back()->with('success', 'Social media links updated successfully.');
    }
}
