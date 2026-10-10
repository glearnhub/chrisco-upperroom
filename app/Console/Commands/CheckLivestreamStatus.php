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

            $isLive = $this->isVideoCurrentlyLive($videoId);

            if ($isLive === null) {
                // Network error — leave stream marked live; log and continue
                Log::warning("livestream:check-status — could not fetch status for video {$videoId}");
                continue;
            }

            if (!$isLive) {
                $ls->update(['is_live' => false]);
                SystemLog::record(
                    'update',
                    'Livestream',
                    "Auto-ended livestream \"{$ls->title}\" (no longer live on YouTube)."
                );
                $this->info("Auto-ended: {$ls->title}");
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
     * Fetch the YouTube watch page and look for the liveBroadcastContent flag
     * that YouTube embeds in its page-level JSON. Returns true if live, false
     * if ended/upcoming/not found, null on network error.
     * No API key required — reads the public watch page HTML.
     */
    private function isVideoCurrentlyLive(string $videoId): ?bool
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders(['Accept-Language' => 'en-US,en;q=0.9'])
                ->withUserAgent('Mozilla/5.0 (compatible; ChriscoCMS/1.0)')
                ->get("https://www.youtube.com/watch?v={$videoId}");

            if (!$response->successful()) {
                return null;
            }

            $html = $response->body();

            // YouTube serialises page data as ytInitialData / ytInitialPlayerResponse JSON
            // The liveBroadcastContent field appears as: "liveBroadcastContent":"live"
            if (preg_match('/"liveBroadcastContent"\s*:\s*"([^"]+)"/', $html, $m)) {
                return $m[1] === 'live';
            }

            // Fallback: older page format uses isLive:true
            if (preg_match('/"isLive"\s*:\s*(true|false)/', $html, $m)) {
                return $m[1] === 'true';
            }

            // Flag not present — stream has ended
            return false;
        } catch (\Throwable) {
            return null;
        }
    }
}
