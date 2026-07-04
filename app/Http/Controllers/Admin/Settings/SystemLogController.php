<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;

class SystemLogController extends Controller
{
    public function index()
    {
        $logs = SystemLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.settings.logs', compact('logs'));
    }
}
