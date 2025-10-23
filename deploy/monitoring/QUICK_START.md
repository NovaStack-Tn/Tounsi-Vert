# Monitoring Stack - Quick Start (2 Minutes)

## Start Everything

### Linux/Mac
```bash
cd deploy/monitoring
chmod +x start-monitoring.sh
./start-monitoring.sh
```

### Windows
```cmd
cd deploy\monitoring
start-monitoring.bat
```

### Manual Start
```bash
cd deploy/monitoring
docker-compose -f docker-compose.monitor.yml up -d
```

## Access Dashboards

### Grafana
```
URL: http://localhost:3000
Username: admin
Password: admin
```

**First login:** Change password when prompted.

### Prometheus
```
URL: http://localhost:9090
```

### cAdvisor (Container Stats)
```
URL: http://localhost:8080
```

## Import Dashboards (30 seconds)

1. Open Grafana → Click **+** → **Import**

2. Enter these dashboard IDs:

   | ID    | Dashboard Name                    |
   |-------|-----------------------------------|
   | 1860  | Node Exporter Full                |
   | 193   | Docker Container & Host Metrics   |
   | 12486 | PHP-FPM Exporter                  |

3. Click **Load** → Select **Prometheus** datasource → **Import**

## Create TounsiVert Dashboard

1. Grafana → **Create** → **Dashboard** → **Add Panel**

2. Add these panels:

### Panel 1: Request Rate
**Query:**
```promql
sum(rate(tounsivert_http_requests_total[5m])) by (route)
```

### Panel 2: Error Rate
**Query:**
```promql
sum(rate(tounsivert_http_requests_total{status=~"5.."}[5m]))
```

### Panel 3: Response Time (95th percentile)
**Query:**
```promql
histogram_quantile(0.95, sum by (le) (rate(tounsivert_http_request_duration_seconds_bucket[5m])))
```

### Panel 4: Top Donations by Organization
**Query:**
```promql
topk(10, tounsivert_donations_total)
```

### Panel 5: CPU Usage
**Query:**
```promql
100 - (avg(irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)
```

### Panel 6: Memory Usage
**Query:**
```promql
100 - ((node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes) * 100)
```

3. Click **Save dashboard** → Name it "TounsiVert Overview"

## Verify Metrics

### Check Prometheus Targets
1. Open http://localhost:9090/targets
2. All targets should show **UP** status:
   - ✅ tounsivert-app
   - ✅ prometheus
   - ✅ node-exporter
   - ✅ cadvisor
   - ✅ php-fpm

### Query Metrics in Prometheus
1. Open http://localhost:9090/graph
2. Try these queries:
   ```promql
   tounsivert_http_requests_total
   tounsivert_donations_total
   up
   ```

## Troubleshooting

### App Metrics Not Showing?

**Check 1:** Is your app running and exposing `/metrics`?
```bash
curl http://localhost:8000/metrics
```

**Check 2:** Connect monitoring to app network
```bash
docker network connect monitoring_monitoring tounsivert-app
```

**Check 3:** Update prometheus.yml target
```yaml
- targets: ['host.docker.internal:8000']  # For local dev
```

### Node Exporter Not Working on Windows?

Edit `docker-compose.monitor.yml`:
```yaml
node-exporter:
  # Remove: pid: "host"
  # Remove: network_mode: "host"
  ports:
    - "9100:9100"
  networks:
    - monitoring
```

### Grafana Login Failed?

Reset password:
```bash
docker exec -it tounsivert-grafana grafana-cli admin reset-admin-password newpassword
```

## Stop Monitoring

```bash
cd deploy/monitoring
docker-compose -f docker-compose.monitor.yml down

# Remove data volumes (optional)
docker-compose -f docker-compose.monitor.yml down -v
```

## Common Commands

```bash
# View logs
docker-compose -f docker-compose.monitor.yml logs -f

# Restart services
docker-compose -f docker-compose.monitor.yml restart

# Update and restart
docker-compose -f docker-compose.monitor.yml pull
docker-compose -f docker-compose.monitor.yml up -d

# Check status
docker-compose -f docker-compose.monitor.yml ps
```

## What's Next?

1. ✅ **Set up alerts** - See `README.md` for alert rules
2. ✅ **Customize dashboards** - Add your own panels
3. ✅ **Configure retention** - Adjust data retention period
4. ✅ **Secure access** - Add authentication/firewall rules

---

**Need help?** See the full documentation in `README.md`
