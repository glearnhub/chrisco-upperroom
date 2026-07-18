<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class CheckVerifyAccess
{
    public function handle(Request $request, Closure $next)
    {
        $enabled = Setting::get('verify_access_enabled', '0');

        if ($enabled !== '1') {
            return $next($request);
        }

        $start = Setting::get('verify_access_start');
        $end   = Setting::get('verify_access_end');
        $now   = now();

        $afterStart = !$start || $now->gte(\Carbon\Carbon::parse($start));
        $beforeEnd  = !$end   || $now->lte(\Carbon\Carbon::parse($end)->endOfDay());

        if ($afterStart && $beforeEnd) {
            return $next($request);
        }

        return response()->view('errors.verify-access-denied', [
            'start' => $start ? \Carbon\Carbon::parse($start)->format('d M Y') : null,
            'end'   => $end   ? \Carbon\Carbon::parse($end)->format('d M Y')   : null,
        ], 403);
    }
}
