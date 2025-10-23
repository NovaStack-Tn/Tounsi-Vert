<?php

namespace App\Monitoring;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;
use Prometheus\RenderTextFormat;
use Prometheus\Counter;
use Prometheus\Histogram;

class Metrics
{
    private static ?CollectorRegistry $registry = null;
    private static ?Counter $httpRequestsCounter = null;
    private static ?Counter $donationsCounter = null;
    private static ?Histogram $httpDurationHistogram = null;

    /**
     * Get or initialize the CollectorRegistry
     */
    private static function getRegistry(): CollectorRegistry
    {
        if (self::$registry === null) {
            // Use Redis adapter if REDIS_URL exists, else InMemory for dev
            $redisUrl = env('REDIS_URL');
            
            if ($redisUrl) {
                // Parse REDIS_URL (format: redis://host:port)
                $parsedUrl = parse_url($redisUrl);
                $host = $parsedUrl['host'] ?? '127.0.0.1';
                $port = $parsedUrl['port'] ?? 6379;
                $password = $parsedUrl['pass'] ?? null;
                $database = ltrim($parsedUrl['path'] ?? '/0', '/');
                
                Redis::setDefaultOptions([
                    'host' => $host,
                    'port' => $port,
                    'password' => $password,
                    'database' => (int)$database,
                    'timeout' => 0.1,
                    'read_timeout' => 10,
                    'persistent_connections' => false,
                ]);
                
                self::$registry = new CollectorRegistry(new Redis());
            } else {
                // Use InMemory storage for development
                self::$registry = new CollectorRegistry(new InMemory());
            }
        }

        return self::$registry;
    }

    /**
     * Get HTTP requests counter
     */
    private static function getHttpRequestsCounter(): Counter
    {
        if (self::$httpRequestsCounter === null) {
            self::$httpRequestsCounter = self::getRegistry()->getOrRegisterCounter(
                'tounsivert',
                'http_requests_total',
                'Total HTTP requests',
                ['method', 'route', 'status']
            );
        }

        return self::$httpRequestsCounter;
    }

    /**
     * Get donations counter
     */
    private static function getDonationsCounter(): Counter
    {
        if (self::$donationsCounter === null) {
            self::$donationsCounter = self::getRegistry()->getOrRegisterCounter(
                'tounsivert',
                'donations_total',
                'Total donations received',
                ['organization_id']
            );
        }

        return self::$donationsCounter;
    }

    /**
     * Get HTTP duration histogram
     */
    private static function getHttpDurationHistogram(): Histogram
    {
        if (self::$httpDurationHistogram === null) {
            self::$httpDurationHistogram = self::getRegistry()->getOrRegisterHistogram(
                'tounsivert',
                'http_request_duration_seconds',
                'HTTP request duration in seconds',
                ['route'],
                [0.01, 0.05, 0.1, 0.3, 0.5, 1, 2]
            );
        }

        return self::$httpDurationHistogram;
    }

    /**
     * Increment HTTP request counter
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $route Route name or path
     * @param int $status HTTP status code
     */
    public static function incHttpRequest(string $method, string $route, int $status): void
    {
        try {
            self::getHttpRequestsCounter()->inc([
                strtoupper($method),
                $route,
                (string)$status
            ]);
        } catch (\Exception $e) {
            // Silent fail to prevent metrics from breaking the app
            \Log::error('Failed to increment HTTP request counter: ' . $e->getMessage());
        }
    }

    /**
     * Observe HTTP request duration
     *
     * @param string $route Route name or path
     * @param float $duration Duration in seconds
     */
    public static function observeHttpDuration(string $route, float $duration): void
    {
        try {
            self::getHttpDurationHistogram()->observe($duration, [$route]);
        } catch (\Exception $e) {
            \Log::error('Failed to observe HTTP duration: ' . $e->getMessage());
        }
    }

    /**
     * Increment donations counter
     * Call this from your Donation events/observers
     *
     * @param int|string $organizationId Organization ID
     */
    public static function incDonation($organizationId): void
    {
        try {
            self::getDonationsCounter()->inc([(string)$organizationId]);
        } catch (\Exception $e) {
            \Log::error('Failed to increment donation counter: ' . $e->getMessage());
        }
    }

    /**
     * Render metrics in Prometheus text format
     *
     * @return string
     */
    public static function render(): string
    {
        try {
            $renderer = new RenderTextFormat();
            return $renderer->render(self::getRegistry()->getMetricFamilySamples());
        } catch (\Exception $e) {
            \Log::error('Failed to render metrics: ' . $e->getMessage());
            return "# Error rendering metrics\n";
        }
    }
}
