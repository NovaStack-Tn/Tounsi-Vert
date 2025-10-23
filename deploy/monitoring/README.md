# TounsiVert Monitoring Stack

Complete monitoring infrastructure for TounsiVert using Prometheus, Grafana, and various exporters.

## Stack Components

### Core Monitoring
- **Prometheus** (`:9090`) - Time-series database and metrics collector
- **Grafana** (`:3000`) - Visualization and dashboards
  - Default credentials: `admin` / `admin`

### Exporters
- **Node Exporter** (`:9100`) - System metrics (CPU, memory, disk, network)
- **cAdvisor** (`:8080`) - Container metrics (Docker stats)
- **PHP-FPM Exporter** (`:9253`) - PHP-FPM pool statistics

### Application Metrics
- **TounsiVert App** (`/metrics`) - Custom business metrics
  - HTTP request rates and latency
  - Donation counters
  - Custom application metrics

## Quick Start

### Prerequisites
- Docker and Docker Compose installed
- TounsiVert application running (for app metrics)

### 1. Start Monitoring Stack

```bash
cd deploy/monitoring
docker-compose -f docker-compose.monitor.yml up -d
```

### 2. Verify Services

```bash
# Check all services are running
docker-compose -f docker-compose.monitor.yml ps

# Expected output:
# tounsivert-prometheus     running   0.0.0.0:9090->9090/tcp
# tounsivert-grafana        running   0.0.0.0:3000->3000/tcp
# tounsivert-cadvisor       running   0.0.0.0:8080->8080/tcp
# tounsivert-php-fpm-exporter running 0.0.0.0:9253->9253/tcp
# tounsivert-node-exporter  running   host mode
```

### 3. Access Dashboards

**Prometheus:**
```
http://localhost:9090
```

**Grafana:**
```
http://localhost:3000
Username: admin
Password: admin
```

**cAdvisor:**
```
http://localhost:8080
```

## Service Details

### Prometheus

**Configuration:** `prometheus.yml`

Scrapes metrics from:
- `tounsivert-app:80/metrics` - Application metrics (15s interval)
- `prometheus:9090` - Self-monitoring (10s interval)
- `node-exporter:9100` - Host system metrics (15s interval)
- `cadvisor:8080` - Container metrics (15s interval)
- `php-fpm-exporter:9253` - PHP-FPM metrics (15s interval)

**Data Retention:** 15 days

**Storage:** `prometheus-data` volume

### Grafana

**Provisioned Datasources:**
- Prometheus (automatically configured)

**Plugins:**
- `grafana-piechart-panel` (auto-installed)

**Storage:** `grafana-data` volume

### Node Exporter

Collects host-level metrics:
- CPU usage and load
- Memory and swap usage
- Disk I/O and space
- Network traffic
- File system stats

**Note:** Uses `host` network mode for accurate host metrics.

### cAdvisor

Monitors Docker containers:
- Container CPU usage
- Memory consumption
- Network I/O
- Disk I/O
- Container states

### PHP-FPM Exporter

Monitors PHP-FPM pools:
- Active processes
- Idle processes
- Queue length
- Max children reached
- Slow requests

**Configuration:** Set `PHP_FPM_SCRAPE_URI` to your PHP-FPM status endpoint.

## Network Configuration

### Connect to Application Network

If your TounsiVert app runs on a different Docker network:

```yaml
# In docker-compose.monitor.yml
networks:
  monitoring:
    external: true
    name: tounsivert_default  # Your app's network name
```

Or add the monitoring network to your app:

```yaml
# In your app's docker-compose.yml
networks:
  - monitoring

networks:
  monitoring:
    external: true
    name: monitoring_monitoring
```

## Grafana Setup

### First Login

1. Open http://localhost:3000
2. Login with `admin` / `admin`
3. Change password when prompted

### Import Dashboards

#### Option 1: Import from Grafana.com

1. Click **+** → **Import**
2. Use these dashboard IDs:
   - **1860** - Node Exporter Full
   - **193** - Docker Container & Host Metrics
   - **12486** - PHP-FPM Exporter

#### Option 2: Create Custom TounsiVert Dashboard

Create panels with these PromQL queries:

**Request Rate:**
```promql
sum(rate(tounsivert_http_requests_total[5m])) by (route)
```

**Error Rate:**
```promql
sum(rate(tounsivert_http_requests_total{status=~"5.."}[5m]))
```

**95th Percentile Latency:**
```promql
histogram_quantile(0.95, sum by (le) (rate(tounsivert_http_request_duration_seconds_bucket[5m])))
```

**Donations by Organization:**
```promql
topk(10, tounsivert_donations_total)
```

**System Memory Usage:**
```promql
100 - ((node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100)
```

**Container CPU Usage:**
```promql
sum(rate(container_cpu_usage_seconds_total{name=~"tounsivert.*"}[5m])) by (name)
```

## Monitoring Best Practices

### 1. Set Up Alerts

Create `deploy/monitoring/alerts/tounsivert.yml`:

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
          summary: "High error rate detected ({{ $value | humanizePercentage }})"

      - alert: HighLatency
        expr: |
          histogram_quantile(0.95, 
            sum by (le) (rate(tounsivert_http_request_duration_seconds_bucket[5m]))
          ) > 2
        for: 10m
        labels:
          severity: warning
        annotations:
          summary: "95th percentile latency above 2s"

      - alert: HighMemoryUsage
        expr: |
          (1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) > 0.9
        for: 5m
        labels:
          severity: warning
        annotations:
          summary: "Memory usage above 90%"

      - alert: ContainerDown
        expr: up{job="cadvisor"} == 0
        for: 2m
        labels:
          severity: critical
        annotations:
          summary: "Container monitoring is down"
```

Add to `prometheus.yml`:
```yaml
rule_files:
  - "alerts/*.yml"
```

### 2. Data Retention

Default: 15 days. Adjust in `docker-compose.monitor.yml`:

```yaml
command:
  - '--storage.tsdb.retention.time=30d'  # 30 days
  - '--storage.tsdb.retention.size=10GB' # or by size
```

### 3. Security

For production:

```yaml
grafana:
  environment:
    - GF_SECURITY_ADMIN_PASSWORD=${GRAFANA_PASSWORD}  # Use secrets
    - GF_USERS_ALLOW_SIGN_UP=false
    - GF_AUTH_ANONYMOUS_ENABLED=false
```

Restrict Prometheus access:

```yaml
prometheus:
  # Add authentication proxy or firewall rules
  ports:
    - "127.0.0.1:9090:9090"  # Localhost only
```

## Troubleshooting

### Prometheus Can't Scrape App Metrics

**Symptom:** `Get "http://app:80/metrics": dial tcp: lookup app`

**Solution 1:** Add app to monitoring network
```bash
docker network connect monitoring_monitoring tounsivert-app
```

**Solution 2:** Use host IP instead of service name
```yaml
# In prometheus.yml
- targets: ['host.docker.internal:8000']
```

### Node Exporter Not Working on Windows

**Issue:** Host network mode not supported on Windows/Mac

**Solution:** Use bridge mode and expose port:
```yaml
node-exporter:
  network_mode: bridge
  ports:
    - "9100:9100"
  # Remove pid: "host"
```

### cAdvisor Permission Denied

**Symptom:** `failed to get cgroup stats: failed to get container`

**Solution:** Run with required privileges (already configured):
```yaml
cadvisor:
  privileged: true
  devices:
    - /dev/kmsg
```

### PHP-FPM Exporter Can't Connect

**Symptom:** `connection refused to tcp://app:9000/status`

**Solution 1:** Enable PHP-FPM status page in `php-fpm.conf`:
```ini
pm.status_path = /status
```

**Solution 2:** Update scrape URI:
```yaml
environment:
  PHP_FPM_SCRAPE_URI: tcp://tounsivert-app:9000/status
```

## Maintenance

### Backup Grafana Dashboards

```bash
# Export all dashboards
docker exec tounsivert-grafana grafana-cli admin export-dashboards > dashboards-backup.json
```

### View Prometheus Targets

```bash
# Check scrape status
curl http://localhost:9090/api/v1/targets | jq .
```

### Clear Old Data

```bash
# Stop Prometheus
docker-compose -f docker-compose.monitor.yml stop prometheus

# Remove data
docker volume rm monitoring_prometheus-data

# Restart
docker-compose -f docker-compose.monitor.yml up -d prometheus
```

## Logs

```bash
# View all logs
docker-compose -f docker-compose.monitor.yml logs -f

# Specific service
docker-compose -f docker-compose.monitor.yml logs -f prometheus
docker-compose -f docker-compose.monitor.yml logs -f grafana
```

## Stop Monitoring Stack

```bash
cd deploy/monitoring
docker-compose -f docker-compose.monitor.yml down

# Remove volumes (data will be lost)
docker-compose -f docker-compose.monitor.yml down -v
```

## Production Deployment

### Recommended Changes

1. **Use environment variables:**
   ```bash
   cp .env.example .env
   # Edit .env with production values
   docker-compose -f docker-compose.monitor.yml --env-file .env up -d
   ```

2. **Enable HTTPS for Grafana:**
   ```yaml
   grafana:
     environment:
       - GF_SERVER_PROTOCOL=https
       - GF_SERVER_CERT_FILE=/etc/grafana/ssl/cert.pem
       - GF_SERVER_CERT_KEY=/etc/grafana/ssl/key.pem
     volumes:
       - ./ssl:/etc/grafana/ssl:ro
   ```

3. **Add Alertmanager:**
   ```yaml
   alertmanager:
     image: prom/alertmanager:latest
     ports:
       - "9093:9093"
     volumes:
       - ./alertmanager.yml:/etc/alertmanager/alertmanager.yml
   ```

4. **Use external Redis/MySQL exporters**

## Resource Requirements

Minimum recommended:
- **CPU:** 2 cores
- **RAM:** 4GB
- **Disk:** 20GB (for metrics storage)

Typical usage:
- Prometheus: ~500MB RAM
- Grafana: ~200MB RAM
- cAdvisor: ~100MB RAM
- Other exporters: ~50MB RAM each

## Additional Resources

- [Prometheus Documentation](https://prometheus.io/docs/)
- [Grafana Dashboards](https://grafana.com/grafana/dashboards/)
- [PromQL Cheat Sheet](https://promlabs.com/promql-cheat-sheet/)
- [Node Exporter Metrics](https://github.com/prometheus/node_exporter)

---

**Version:** 1.0  
**Last Updated:** 2025-10-24  
**Maintained by:** TounsiVert DevOps Team
