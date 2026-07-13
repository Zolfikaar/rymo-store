<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Copy to: app/Console/Commands/UrPulseHeartbeat.php
 *
 * Env (optional):
 *   URPULSE_CORE_URL=http://localhost:5252
 *   URPULSE_APP_ID=rymo-store
 *   URPULSE_SERVICE_NAME=Backend-API
 *
 * Run: php artisan pulse:heartbeat
 */
class UrPulseHeartbeat extends Command
{
    protected $signature = 'pulse:heartbeat';

    protected $description = 'Send continuous server-side heartbeat pulses to UrPulse Core (interval from Core client-config)';

    public function handle(): int
    {
        $coreUrl = rtrim((string) env('URPULSE_CORE_URL', 'http://localhost:5252'), '/');
        $appId = (string) env('URPULSE_APP_ID', 'rymo-store');
        $serviceName = (string) env('URPULSE_SERVICE_NAME', 'Backend-API');
        $intervalSeconds = 10;
        $loopsSinceConfigRefresh = 0;

        $this->info("UrPulse Heartbeat active for {$appId}:{$serviceName} → {$coreUrl}");
        $this->info('Interval is controlled by UrPulse Core (GET /api/pulse/client-config). Press Ctrl+C to stop.');

        while (true) {
            if ($loopsSinceConfigRefresh === 0) {
                $intervalSeconds = $this->fetchHeartbeatInterval($coreUrl, $intervalSeconds);
            }

            $loopsSinceConfigRefresh = ($loopsSinceConfigRefresh + 1) % max(1, (int) ceil(60 / max(1, $intervalSeconds)));

            try {
                $response = Http::timeout(5)->post("{$coreUrl}/api/pulse/heartbeat", [
                    'appId' => $appId,
                    'serviceName' => $serviceName,
                    'status' => 'Healthy',
                    'metadata' => [
                        'php_version' => PHP_VERSION,
                        'environment' => app()->environment(),
                        'runtime' => 'artisan',
                    ],
                ]);

                if (! $response->successful()) {
                    $this->warn("UrPulse rejected heartbeat ({$response->status()}).");
                }
            } catch (\Throwable $e) {
                $this->error('UrPulse Core unreachable.');
            }

            sleep($intervalSeconds);
        }
    }

    private function fetchHeartbeatInterval(string $coreUrl, int $fallback): int
    {
        try {
            $response = Http::timeout(3)->get("{$coreUrl}/api/pulse/client-config");
            if ($response->successful()) {
                $seconds = (int) ($response->json('heartbeatIntervalSeconds') ?? $fallback);
                $clamped = max(5, min(60, $seconds));
                if ($clamped !== $fallback) {
                    $this->info("Synced heartbeat interval from Core: {$clamped}s");
                }

                return $clamped;
            }
        } catch (\Throwable $e) {
            // Keep last known interval
        }

        return $fallback;
    }
}
