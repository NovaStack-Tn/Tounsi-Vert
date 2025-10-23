<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Monitoring\Metrics;

class RequestMetrics
{
    /**
     * Handle an incoming request and track metrics
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start timer
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate duration in seconds
        $duration = microtime(true) - $startTime;

        // Get route name or use path as fallback
        $route = $request->route() ? $request->route()->getName() : 'unknown';
        if (empty($route)) {
            $route = $request->path();
        }
        
        // Skip metrics endpoint itself to avoid recursion
        if ($route === 'metrics' || $request->path() === 'metrics') {
            return $response;
        }

        // Get HTTP method
        $method = $request->method();

        // Get status code
        $status = $response->getStatusCode();

        // Record metrics
        Metrics::incHttpRequest($method, $route, $status);
        Metrics::observeHttpDuration($route, $duration);

        return $response;
    }
}
