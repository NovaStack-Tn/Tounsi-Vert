#!/bin/bash
# TounsiVert Monitoring Stack Startup Script

set -e

echo "🚀 Starting TounsiVert Monitoring Stack..."
echo ""

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

# Create necessary directories
echo "📁 Creating directories..."
mkdir -p grafana/provisioning/datasources
mkdir -p grafana/provisioning/dashboards/json
mkdir -p alerts

# Check if .env exists
if [ ! -f .env ]; then
    echo "⚠️  No .env file found. Using defaults from .env.example"
    cp .env.example .env
fi

# Start the monitoring stack
echo "🐳 Starting Docker containers..."
docker-compose -f docker-compose.monitor.yml up -d

# Wait for services to be ready
echo ""
echo "⏳ Waiting for services to start..."
sleep 5

# Check service health
echo ""
echo "🔍 Checking service status..."
docker-compose -f docker-compose.monitor.yml ps

# Display access information
echo ""
echo "✅ Monitoring stack is running!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 Access URLs:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Grafana:         http://localhost:3000"
echo "                   └─ Login: admin / admin"
echo ""
echo "  Prometheus:      http://localhost:9090"
echo "  cAdvisor:        http://localhost:8080"
echo "  PHP-FPM Metrics: http://localhost:9253/metrics"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📖 Next steps:"
echo "  1. Open Grafana and change the admin password"
echo "  2. Import dashboards (see README.md for dashboard IDs)"
echo "  3. Configure alerts in prometheus.yml"
echo ""
echo "🛠️  Commands:"
echo "  View logs:  docker-compose -f docker-compose.monitor.yml logs -f"
echo "  Stop:       docker-compose -f docker-compose.monitor.yml down"
echo "  Restart:    docker-compose -f docker-compose.monitor.yml restart"
echo ""
