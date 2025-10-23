# Prometheus Metrics - Quick Start Guide

## Installation (5 minutes)

### Step 1: Install Dependencies
```bash
cd backend
composer install
```

This will install `promphp/prometheus_client_php` and all dependencies.

### Step 2: Configure Environment
Add to your `.env`:

```env
# For Development (InMemory storage)
REDIS_URL=

# For Production (Redis storage)
REDIS_URL=redis://127.0.0.1:6379/0

# Security (default: only local networks allowed)
METRICS_AUTH_GATE=false
```

### Step 3: Test the Endpoint
Start your Laravel server:
```bash
php artisan serve
```

Access metrics:
```bash
curl http://localhost:8000/metrics
```

**Expected output:**
```
# HELP tounsivert_http_requests_total Total HTTP requests
# TYPE tounsivert_http_requests_total counter
tounsivert_http_requests_total{method="GET",route="home",status="200"} 1

# HELP tounsivert_http_request_duration_seconds HTTP request duration in seconds
# TYPE tounsivert_http_request_duration_seconds histogram
...
```

## What's Included

### ✅ Automatic Metrics
All HTTP requests are automatically tracked with:
- **Request counter** by method, route, and status
- **Response time histogram** by route
- No code changes needed!

### ✅ Business Metrics
Donations are automatically tracked via Observer:
- Counter increments on every new donation
- Labeled by organization_id

### ✅ Security
- Protected by IP allowlist (local networks only)
- Or set `METRICS_AUTH_GATE=true` for open access

## Usage Examples

### Track Custom Business Events

Add to any controller or service:

```php
use App\Monitoring\Metrics;

// Track a donation
Metrics::incDonation($organizationId);

// Manually track HTTP request (not needed, done automatically)
Metrics::incHttpRequest('POST', 'donations.store', 201);

// Observe request duration (not needed, done automatically)
Metrics::observeHttpDuration('events.show', 0.125);
```

### View Metrics in Real-Time

```bash
# Watch metrics update
watch -n 1 curl -s http://localhost:8000/metrics
```

### Test with curl

```bash
# Generate some traffic
for i in {1..10}; do
  curl http://localhost:8000/
  curl http://localhost:8000/events
  curl http://localhost:8000/organizations
done

# View the metrics
curl http://localhost:8000/metrics | grep http_requests_total
```

## Production Setup

### With Redis

1. **Install Redis:**
   ```bash
   # Ubuntu/Debian
   sudo apt install redis-server
   
   # macOS
   brew install redis
   ```

2. **Update .env:**
   ```env
   REDIS_URL=redis://127.0.0.1:6379/0
   ```

3. **Restart app:**
   ```bash
   php artisan config:clear
   php artisan serve
   ```

### With Docker Compose

Add to `docker-compose.yml`:

```yaml
services:
  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data

  prometheus:
    image: prom/prometheus:latest
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'

volumes:
  redis-data:
```

Create `prometheus.yml`:
```yaml
global:
  scrape_interval: 15s

scrape_configs:
  - job_name: 'tounsivert'
    static_configs:
      - targets: ['app:80']
    metrics_path: '/metrics'
```

## Verification Checklist

- [ ] `/metrics` endpoint returns text/plain with Prometheus format
- [ ] HTTP requests are counted after browsing the site
- [ ] Request duration histogram shows buckets
- [ ] Donations increment counter (test by creating a donation)
- [ ] Metrics persist after refresh (if using Redis)

## Troubleshooting

**403 Forbidden?**
- Set `METRICS_AUTH_GATE=true` or access from localhost

**Metrics not appearing?**
- Check middleware is registered: `php artisan route:list | grep metrics`
- Check logs: `tail -f storage/logs/laravel.log`

**Metrics reset on restart?**
- You're using InMemory storage (dev mode)
- Set `REDIS_URL` to persist metrics

## Next Steps

1. **Set up Prometheus scraping** - See `PROMETHEUS_METRICS.md`
2. **Create Grafana dashboards** - Visualize your metrics
3. **Set up alerts** - Get notified of issues
4. **Add custom metrics** - Track your business KPIs

## File Structure

```
backend/
├── app/
│   ├── Monitoring/
│   │   └── Metrics.php              # Main metrics service
│   ├── Http/
│   │   ├── Middleware/
│   │   │   └── RequestMetrics.php   # Auto-tracking middleware
│   │   └── Controllers/
│   │       └── MetricsController.php # /metrics endpoint
│   ├── Observers/
│   │   └── DonationObserver.php     # Auto-track donations
│   └── Providers/
│       └── EventServiceProvider.php  # Observer registration
├── routes/
│   └── web.php                       # /metrics route
└── .env                              # Configuration
```

## Support

See detailed documentation: `PROMETHEUS_METRICS.md`

---

**Ready to monitor!** 🚀
