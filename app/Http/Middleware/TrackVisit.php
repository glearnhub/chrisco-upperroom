<?php

namespace App\Http\Middleware;

use App\Jobs\ResolveVisitGeo;
use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TrackVisit
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (
            $request->isMethod('GET') &&
            !$request->expectsJson() &&
            !$request->is('admin/*') &&
            !$request->is('cur_admin*') &&
            !$request->is('verify-email*') &&
            !$request->is('confirm-password*') &&
            $response->isSuccessful()
        ) {
            $sessionId = $request->session()->getId();
            $ip        = $request->ip();
            $cacheKey  = 'visit_session_' . $sessionId . '_' . today()->toDateString();
            $isNew     = !Cache::has($cacheKey);

            if ($isNew) {
                Cache::put($cacheKey, true, now()->endOfDay());
            }

            // For local addresses resolve synchronously (no HTTP call needed)
            $isLocal = in_array($ip, ['127.0.0.1', '::1', 'localhost']);

            $visit = SiteVisit::create([
                'path'           => '/' . $request->path(),
                'ip'             => $ip,
                'session_id'     => $sessionId,
                'country'        => $isLocal ? 'Local' : null,
                'country_code'   => $isLocal ? 'LC'    : null,
                'is_new_session' => $isNew,
            ]);

            // Dispatch geo-lookup as a background job for non-local new sessions
            if ($isNew && !$isLocal) {
                ResolveVisitGeo::dispatch($visit->id, $ip);
            }
        }

        return $response;
    }
}
