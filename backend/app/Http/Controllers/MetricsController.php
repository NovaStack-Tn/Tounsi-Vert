<?php

namespace App\Http\Controllers;

use App\Monitoring\Metrics;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MetricsController extends Controller
{
    /**
     * Serve Prometheus metrics
     * Protected by IP allowlist or AUTH_GATE env variable
     */
    public function index(Request $request): Response
    {
        // Check authorization
        if (!$this->isAuthorized($request)) {
            abort(403, 'Access denied to metrics endpoint');
        }

        // Render metrics in Prometheus text format
        $metrics = Metrics::render();

        return response($metrics, 200)
            ->header('Content-Type', 'text/plain; version=0.0.4');
    }

    /**
     * Check if request is authorized to access metrics
     */
    private function isAuthorized(Request $request): bool
    {
        // If AUTH_GATE is explicitly set to true, allow access
        if (env('METRICS_AUTH_GATE', false) === true || env('METRICS_AUTH_GATE') === 'true') {
            return true;
        }

        // Get client IP
        $clientIp = $request->ip();

        // Allow localhost
        if (in_array($clientIp, ['127.0.0.1', '::1', 'localhost'])) {
            return true;
        }

        // Check if IP is in private network ranges
        return $this->isPrivateNetwork($clientIp);
    }

    /**
     * Check if IP is in private network ranges
     * Supports: 10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16
     */
    private function isPrivateNetwork(string $ip): bool
    {
        // Convert IP to long for comparison
        $ipLong = ip2long($ip);
        
        if ($ipLong === false) {
            return false;
        }

        // Define private network ranges
        $privateRanges = [
            ['10.0.0.0', '10.255.255.255'],       // 10.0.0.0/8
            ['172.16.0.0', '172.31.255.255'],     // 172.16.0.0/12
            ['192.168.0.0', '192.168.255.255'],   // 192.168.0.0/16
        ];

        foreach ($privateRanges as $range) {
            $start = ip2long($range[0]);
            $end = ip2long($range[1]);

            if ($ipLong >= $start && $ipLong <= $end) {
                return true;
            }
        }

        return false;
    }
}
