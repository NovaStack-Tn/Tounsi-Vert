# Prometheus Metrics Integration

## Overview
TounsiVert includes built-in Prometheus metrics for monitoring application performance, request statistics, and business metrics like donations.

## Installation

### 1. Install Dependencies
```bash
cd backend
composer install
```

The `promphp/prometheus_client_php` package is already included in `composer.json`.

### 2. Configure Environment Variables

Add to your `.env` file:

```env
# Prometheus Metrics Configuration
# REDIS_URL for production (shared metrics across instances)
REDIS_URL=redis://127.0.0.1:6379/0

# Set to true to allow public access to /metrics (use with caution in production)
METRICS_AUTH_GATE=false
```

**Storage Adapters:**
- **Redis** (Production): Uses Redis for persistent metrics storage across multiple app instances
  - Set `REDIS_URL=redis://host:port/database`
  - Recommended for production environments
- **InMemory** (Development): Uses in-memory storage (metrics reset on restart)
  - Leave `REDIS_URL` empty
  - Simple setup for local development

## Metrics Endpoint

### Access URL
```
GET /metrics
```

### Security
The `/metrics` endpoint is protected by default:

1. **Local Network Access** (Default)
   - Allows requests from:
     - `127.0.0.1` (localhost)
     - `10.0.0.0/8` (private network)
     - `172.16.0.0/12` (private network)
     - `192.168.0.0/16` (private network)

2. **Public Access** (Use with caution)
   - Set `METRICS_AUTH_GATE=true` in `.env` to allow public access
   - **Warning**: Only enable in trusted environments or behind firewall

### Example Response
```
# HELP tounsivert_http_requests_total Total HTTP requests
# TYPE tounsivert_http_requests_total counter
tounsivert_http_requests_total{method="GET",route="home",status="200"} 1523
tounsivert_http_requests_total{method="POST",route="donations.store",status="201"} 42

# HELP tounsivert_donations_total Total donations received
# TYPE tounsivert_donations_total counter
tounsivert_donations_total{organization_id="5"} 127
tounsivert_donations_total{organization_id="12"} 89

# HELP tounsivert_http_request_duration_seconds HTTP request duration in seconds
# TYPE tounsivert_http_request_duration_seconds histogram
tounsivert_http_request_duration_seconds_bucket{route="events.index",le="0.01"} 45
tounsivert_http_request_duration_seconds_bucket{route="events.index",le="0.05"} 120
tounsivert_http_request_duration_seconds_bucket{route="events.index",le="0.1"} 180
tounsivert_http_request_duration_seconds_bucket{route="events.index",le="+Inf"} 200
tounsivert_http_request_duration_seconds_sum{route="events.index"} 8.5
tounsivert_http_request_duration_seconds_count{route="events.index"} 200
```

## Available Metrics

### 1. HTTP Request Counter
**Name:** `tounsivert_http_requests_total`  
**Type:** Counter  
**Labels:**
- `method`: HTTP method (GET, POST, PUT, DELETE, etc.)
- `route`: Route name or path
- `status`: HTTP status code (200, 404, 500, etc.)

**Description:** Tracks total number of HTTP requests by method, route, and status code.

### 2. HTTP Request Duration
**Name:** `tounsivert_http_request_duration_seconds`  
**Type:** Histogram  
**Labels:**
- `route`: Route name or path

**Buckets:** `[0.01, 0.05, 0.1, 0.3, 0.5, 1, 2]` seconds

**Description:** Measures HTTP request duration in seconds with predefined buckets for latency analysis.

### 3. Donations Counter
**Name:** `tounsivert_donations_total`  
**Type:** Counter  
**Labels:**
- `organization_id`: ID of the organization receiving the donation

**Description:** Tracks total number of donations per organization.

## Custom Business Metrics

### Tracking Donations

To track donations from your application code, use the `Metrics::incDonation()` method:

```php
use App\Monitoring\Metrics;

// In your DonationController or Observer
public function store(Request $request)
{
    $donation = Donation::create([
        'organization_id' => $request->organization_id,
        'amount' => $request->amount,
        // ... other fields
    ]);

    // Track donation metric
    Metrics::incDonation($donation->organization_id);

    return redirect()->back()->with('success', 'Donation recorded!');
}
```

### Using Observers (Recommended)

Create an observer to automatically track donations:

```bash
php artisan make:observer DonationObserver --model=Donation
```

**app/Observers/DonationObserver.php:**
```php
<?php

namespace App\Observers;

use App\Models\Donation;
use App\Monitoring\Metrics;

class DonationObserver
{
    public function created(Donation $donation): void
    {
        // Automatically track donation when created
        Metrics::incDonation($donation->organization_id);
    }
}
```

**Register observer in `app/Providers/EventServiceProvider.php`:**
```php
use App\Models\Donation;
use App\Observers\DonationObserver;

public function boot(): void
{
    Donation::observe(DonationObserver::class);
}
```

### Adding Custom Metrics

You can extend the `Metrics` class to add more business metrics:

```php
// In app/Monitoring/Metrics.php

/**
 * Track event participation
 */
public static function incEventParticipation($eventId): void
{
    try {
        $counter = self::getRegistry()->getOrRegisterCounter(
            'tounsivert',
            'event_participations_total',
            'Total event participations',
            ['event_id']
        );
        $counter->inc([(string)$eventId]);
    } catch (\Exception $e) {
        \Log::error('Failed to increment participation counter: ' . $e->getMessage());
    }
}
```

## Prometheus Configuration

### Scrape Config
Add to your `prometheus.yml`:

```yaml
scrape_configs:
  - job_name: 'tounsivert'
    scrape_interval: 15s
    static_configs:
      - targets: ['your-app-url:80']
    metrics_path: '/metrics'
```

### Docker Compose Example

```yaml
version: '3.8'

services:
  app:
    image: tounsivert:latest
    environment:
      - REDIS_URL=redis://redis:6379/0
      - METRICS_AUTH_GATE=false
    ports:
      - "80:80"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  prometheus:
    image: prom/prometheus:latest
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml
      - prometheus-data:/prometheus
    ports:
      - "9090:9090"
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'

  grafana:
    image: grafana/grafana:latest
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana-data:/var/lib/grafana

volumes:
  prometheus-data:
  grafana-data:
```

## Grafana Dashboards

### Sample Queries

**Request Rate (requests/second):**
```promql
rate(tounsivert_http_requests_total[5m])
```

**Request Rate by Route:**
```promql
sum by (route) (rate(tounsivert_http_requests_total[5m]))
```

**Error Rate (4xx + 5xx):**
```promql
sum(rate(tounsivert_http_requests_total{status=~"4..|5.."}[5m]))
```

**Average Request Duration:**
```promql
rate(tounsivert_http_request_duration_seconds_sum[5m])
/
rate(tounsivert_http_request_duration_seconds_count[5m])
```

**95th Percentile Latency:**
```promql
histogram_quantile(0.95, sum by (le) (rate(tounsivert_http_request_duration_seconds_bucket[5m])))
```

**Donations per Organization:**
```promql
topk(10, tounsivert_donations_total)
```

**Donation Growth Rate:**
```promql
rate(tounsivert_donations_total[1h])
```

## Kubernetes Deployment

### Service Monitor (Prometheus Operator)

```yaml
apiVersion: monitoring.coreos.com/v1
kind: ServiceMonitor
metadata:
  name: tounsivert
  labels:
    app: tounsivert
spec:
  selector:
    matchLabels:
      app: tounsivert
  endpoints:
    - port: http
      path: /metrics
      interval: 30s
```

## Troubleshooting

### Metrics Not Appearing

1. **Check endpoint is accessible:**
   ```bash
   curl http://localhost/metrics
   ```

2. **Verify middleware is registered:**
   ```bash
   php artisan route:list | grep metrics
   ```

3. **Check logs for errors:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### 403 Forbidden on /metrics

- Verify your IP is in allowed ranges (local network)
- Or set `METRICS_AUTH_GATE=true` for testing

### Metrics Reset After Restart

- Using InMemory storage (dev mode)
- Solution: Set `REDIS_URL` to persist metrics

### Redis Connection Issues

```bash
# Test Redis connection
redis-cli -h 127.0.0.1 -p 6379 ping
# Should return: PONG

# Check Redis keys
redis-cli -h 127.0.0.1 -p 6379 keys "PROMETHEUS*"
```

## Performance Considerations

1. **Minimal Overhead:** Metrics collection adds ~1-3ms per request
2. **Redis vs InMemory:** Redis adds negligible latency (~0.5ms)
3. **High-Cardinality Warning:** Avoid unbounded label values (e.g., user IDs)
4. **Scrape Interval:** 15-30s is recommended

## Best Practices

1. **Use Redis in Production:** Ensures metrics persist across deployments
2. **Limit Label Cardinality:** Keep label values bounded (routes, status codes)
3. **Monitor the Metrics:** Set up alerts in Prometheus for critical thresholds
4. **Secure the Endpoint:** Use network policies or VPN for production access
5. **Regular Pruning:** Old metrics are automatically cleaned by Prometheus retention

## Example Alerts (Prometheus)

```yaml
groups:
  - name: tounsivert_alerts
    rules:
      - alert: HighErrorRate
        expr: |
          sum(rate(tounsivert_http_requests_total{status=~"5.."}[5m]))
          / sum(rate(tounsivert_http_requests_total[5m])) > 0.05
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "High error rate detected"

      - alert: SlowRequests
        expr: |
          histogram_quantile(0.95, 
            sum by (le) (rate(tounsivert_http_request_duration_seconds_bucket[5m]))
          ) > 2
        for: 10m
        labels:
          severity: warning
        annotations:
          summary: "95th percentile latency above 2s"
```

## Support

For issues or questions:
- Check Laravel logs: `storage/logs/laravel.log`
- Review Prometheus docs: https://prometheus.io/docs/
- Grafana tutorials: https://grafana.com/tutorials/

---

**Last Updated:** 2025-10-23  
**Version:** 1.0  
**Maintained by:** TounsiVert Development Team
