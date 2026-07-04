<?php

namespace App\Http\Middleware;

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
            $sessionId  = $request->session()->getId();
            $ip         = $request->ip();
            $cacheKey   = 'visit_session_' . $sessionId . '_' . today()->toDateString();
            $isNew      = !Cache::has($cacheKey);

            $country     = null;
            $countryCode = null;

            if ($isNew) {
                // Mark session as seen for today
                Cache::put($cacheKey, true, now()->endOfDay());

                // Geo-lookup for new sessions only (cached per IP for 24h)
                [$country, $countryCode] = $this->getCountry($ip);
            }

            SiteVisit::create([
                'path'           => '/' . $request->path(),
                'ip'             => $ip,
                'session_id'     => $sessionId,
                'country'        => $country,
                'country_code'   => $countryCode,
                'is_new_session' => $isNew,
            ]);
        }

        return $response;
    }

    private function getCountry(string $ip): array
    {
        // Skip localhost
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return ['Local', 'LC'];
        }

        return Cache::remember('geoip_' . $ip, now()->addDay(), function () use ($ip) {
            try {
                $response = @file_get_contents(
                    "http://ip-api.com/json/{$ip}?fields=country,countryCode",
                    false,
                    stream_context_create(['http' => ['timeout' => 2]])
                );
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['country'])) {
                        return [$data['country'], $data['countryCode']];
                    }
                }
            } catch (\Throwable) {}
            return [null, null];
        });
    }
}
