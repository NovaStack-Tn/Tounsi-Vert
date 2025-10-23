@echo off
REM TounsiVert Monitoring Stack Startup Script for Windows

echo.
echo Starting TounsiVert Monitoring Stack...
echo.

REM Check if Docker is running
docker info >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Docker is not running. Please start Docker Desktop first.
    pause
    exit /b 1
)

REM Create necessary directories
echo Creating directories...
if not exist "grafana\provisioning\datasources" mkdir grafana\provisioning\datasources
if not exist "grafana\provisioning\dashboards\json" mkdir grafana\provisioning\dashboards\json
if not exist "alerts" mkdir alerts

REM Check if .env exists
if not exist ".env" (
    echo Warning: No .env file found. Using defaults from .env.example
    copy .env.example .env
)

REM Start the monitoring stack
echo.
echo Starting Docker containers...
docker-compose -f docker-compose.monitor.yml up -d

REM Wait for services to be ready
echo.
echo Waiting for services to start...
timeout /t 5 /nobreak >nul

REM Check service status
echo.
echo Checking service status...
docker-compose -f docker-compose.monitor.yml ps

REM Display access information
echo.
echo ================================================================
echo Monitoring stack is running!
echo ================================================================
echo.
echo Access URLs:
echo   Grafana:         http://localhost:3000
echo                    (Login: admin / admin)
echo.
echo   Prometheus:      http://localhost:9090
echo   cAdvisor:        http://localhost:8080
echo   PHP-FPM Metrics: http://localhost:9253/metrics
echo ================================================================
echo.
echo Next steps:
echo   1. Open Grafana and change the admin password
echo   2. Import dashboards (see README.md for dashboard IDs)
echo   3. Configure alerts in prometheus.yml
echo.
echo Commands:
echo   View logs:  docker-compose -f docker-compose.monitor.yml logs -f
echo   Stop:       docker-compose -f docker-compose.monitor.yml down
echo   Restart:    docker-compose -f docker-compose.monitor.yml restart
echo.
pause
