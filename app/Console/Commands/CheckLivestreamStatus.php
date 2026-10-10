<?php

namespace App\Console\Commands;

use App\Models\Livestream;
use App\Models\SystemLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckLivestreamStatus extends Command
{
    protected $signature   = 'livestream:check-status';
    protected $description = 'Auto-end any active YouTube livestreams that are no longer live on YouTube';

    public function handle(): int
    {
        $apiKey = config('services.youtube.api_key');

        if (!$apiKey) {
            $this->warn('YOUTUBE_API_KEY not set — skipping auto-end check.');
            return self::SUCCESS;
        }

        $active = Livestream::where('is_live', true)->get();

        if ($active->isEmpty()) {
            return self::SUCCESS;
        }

        foreach ($active as $ls) {
            $videoId = $this->extractVideoId($ls->embed_url);

            if (!$videoId) {
                // Non-YouTube URL (e.g. Facebook) — cannot auto-check; skip
                continue;
            }

            $status = $this->fetchLiveBroadcastStatus($videoId, $apiKey);

            if ($status === null) {
                // API error — leave stream marked live; log and continue
                Log::warning("livestream:check-status — could not fetch status for video {$videoId}");
                continue;
            }

            if ($status !== 'live') {
                $ls->update(['is_live' => false]);
                SystemLog::record(
                    'update',
                    'Livestream',
                    "Auto-ended livestream \"{$ls->title}\" (YouTube status: {$status})."
                );
                $this->info("Auto-ended: {$ls->title} (status: {$status})");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Extract the YouTube video ID from a watch, short, or embed URL.
     * Returns null for non-YouTube or unrecognisable URLs.
     */
    private function extractVideoId(string $url): ?string
    {
        // youtu.be/<id>
        if (preg_match('#youtu\.be/([A-Za-z0-9_\-]{11})#', $url, $m)) {
            return $m[1];
        }
        // youtube.com/watch?v=<id>  or  youtube.com/live/<id>
        if (preg_match('#youtube\.com/(?:watch\?(?:[^&]*&)*v=|live/)([A-Za-z0-9_\-]{11})#', $url, $m)) {
            return $m[1];
        }
        // youtube.com/embed/<id>
        if (preg_match('#youtube\.com/embed/([A-Za-z0-9_\-]{11})#', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Call YouTube Data API and return liveBroadcastContent: 'live', 'upcoming', or 'none'.
     * Returns null on any HTTP / API error.
     */
    private function fetchLiveBroadcastStatus(string $videoId, string $apiKey): ?string
    {
        try {
            $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/videos', [
                'part' => 'snippet',
                'id'   => $videoId,
                'key'  => $apiKey,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $items = $response->json('items', []);

            if (empty($items)) {
                // Video deleted or private — treat as ended
                return 'none';
            }

            return $items[0]['snippet']['liveBroadcastContent'] ?? 'none';
        } catch (\Throwable) {
            return null;
        }
    }
}
