<?php

namespace App\Jobs;

use App\Models\SiteVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ResolveVisitGeo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 10;

    public function __construct(
        public int    $visitId,
        public string $ip
    ) {}

    public function handle(): void
    {
        $visit = SiteVisit::find($this->visitId);
        if (!$visit || $visit->country !== null) {
            return;
        }

        [$country, $countryCode] = Cache::remember('geoip_' . $this->ip, now()->addDay(), function () {
            try {
                $response = @file_get_contents(
                    "https://ip-api.com/json/{$this->ip}?fields=country,countryCode",
                    false,
                    stream_context_create(['http' => ['timeout' => 3]])
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

        $visit->update(['country' => $country, 'country_code' => $countryCode]);
    }

    public function failed(\Throwable $e): void
    {
        \Log::warning("ResolveVisitGeo failed for visit {$this->visitId} (IP: {$this->ip}): " . $e->getMessage());
    }
}
