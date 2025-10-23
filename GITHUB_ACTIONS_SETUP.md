# GitHub Actions CI/CD Setup Guide

## Overview
This guide explains how to configure and use the GitHub Actions CI/CD pipeline for the TounsiVert Laravel 10 project.

## Workflow File Location
```
.github/workflows/laravel-ci-cd.yml
```

## Pipeline Features

### ✅ Automated Testing
- PHP 8.1 environment setup
- MySQL 8.0 test database
- Composer dependency installation
- Laravel migrations
- PHPUnit test execution

### ✅ Code Quality
- SonarQube integration for code analysis
- Automated quality gate checks

### ✅ Frontend Build (Optional)
- Automatic detection of `package.json`
- Node.js 18 setup
- npm install and build

### ✅ Docker Deployment
- Multi-stage Docker build
- Push to Docker Hub with tags:
  - `latest` (most recent)
  - `<commit-sha>` (specific version)

### ✅ Status Reporting
- Success/failure summaries in GitHub Actions UI
- Detailed logs for debugging

---

## Required GitHub Secrets

Navigate to your repository: **Settings → Secrets and variables → Actions → New repository secret**

### 1. SonarQube Configuration
```
SONAR_TOKEN=<your-sonarqube-token>
SONAR_HOST_URL=<your-sonarqube-server-url>
```

**How to get SonarQube token:**
1. Log in to your SonarQube instance
2. Go to **My Account → Security → Generate Tokens**
3. Generate a new token for the TounsiVert project
4. Copy the token and add it to GitHub Secrets

**Example:**
```
SONAR_TOKEN=sqp_1234567890abcdef
SONAR_HOST_URL=https://sonarcloud.io
```

### 2. Docker Hub Configuration
```
DOCKER_USERNAME=<your-dockerhub-username>
DOCKER_PASSWORD=<your-dockerhub-password-or-token>
```

**How to get Docker Hub credentials:**
1. Log in to [Docker Hub](https://hub.docker.com/)
2. Go to **Account Settings → Security → New Access Token**
3. Create a token with **Read & Write** permissions
4. Use your username and the token as the password

**Example:**
```
DOCKER_USERNAME=yourusername
DOCKER_PASSWORD=dckr_pat_1234567890abcdef
```

---

## SonarQube Configuration File

Create a `sonar-project.properties` file in the root of your repository:

```properties
# SonarQube Project Configuration
sonar.projectKey=tounsivert
sonar.projectName=TounsiVert
sonar.projectVersion=1.0

# Source code paths
sonar.sources=backend/app
sonar.tests=backend/tests

# Exclusions
sonar.exclusions=**/vendor/**,**/node_modules/**,**/storage/**,**/bootstrap/cache/**

# PHP specific
sonar.php.coverage.reportPaths=backend/coverage.xml
sonar.php.tests.reportPath=backend/phpunit.xml

# Encoding
sonar.sourceEncoding=UTF-8
```

---

## Triggering the Pipeline

### Automatic Triggers
The pipeline runs automatically on:
- **Push to `main` branch**
- **Pull Request targeting `main` branch**

### Manual Trigger
You can manually trigger the workflow from the GitHub Actions UI:
1. Go to **Actions** tab
2. Select **Laravel CI/CD Pipeline**
3. Click **Run workflow**

---

## Pipeline Steps Breakdown

### 1. **Checkout Code**
Clones the repository to the runner.

### 2. **Setup PHP 8.1**
Installs PHP with required extensions:
- mbstring
- pdo_mysql
- intl
- gd
- xml

### 3. **Install Composer Dependencies**
```bash
composer install --no-progress --no-interaction --prefer-dist --optimize-autoloader
```

### 4. **Setup Environment**
```bash
cp .env.example .env
php artisan key:generate
```

### 5. **Configure Database**
Sets up MySQL connection for testing.

### 6. **Run Migrations**
```bash
php artisan migrate --force
```

### 7. **Run Tests**
```bash
php artisan test
```

### 8. **Frontend Build (Optional)**
If `package.json` exists:
```bash
npm install
npm run build
```

### 9. **SonarQube Scan**
Analyzes code quality and security vulnerabilities.

### 10. **Docker Build & Push**
Builds Docker image and pushes to Docker Hub with versioned tags.

---

## Monitoring Pipeline Execution

### View Pipeline Status
1. Go to the **Actions** tab in your repository
2. Click on the workflow run
3. View detailed logs for each step

### Success Indicators
- ✅ All steps show green checkmarks
- Docker image pushed successfully
- SonarQube quality gate passed

### Failure Handling
If a step fails:
1. Check the step's detailed logs
2. Review the error message
3. Fix the issue in your code
4. Commit and push again

---

## Docker Deployment

### Pull the Image
After a successful pipeline run:
```bash
docker pull <your-dockerhub-username>/tounsivert:latest
```

### Run the Container
```bash
docker run -d \
  -p 9000:9000 \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=your-db-name \
  -e DB_USERNAME=your-db-user \
  -e DB_PASSWORD=your-db-pass \
  --name tounsivert \
  <your-dockerhub-username>/tounsivert:latest
```

### Docker Compose (Production)
Create `docker-compose.prod.yml`:
```yaml
version: '3.8'

services:
  app:
    image: <your-dockerhub-username>/tounsivert:latest
    ports:
      - "9000:9000"
    environment:
      - DB_HOST=mysql
      - DB_DATABASE=tounsivert
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - mysql

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: tounsivert
    volumes:
      - mysql-data:/var/lib/mysql

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
    volumes:
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - app

volumes:
  mysql-data:
```

Run with:
```bash
docker-compose -f docker-compose.prod.yml up -d
```

---

## Troubleshooting

### Common Issues

#### 1. **Tests Failing**
```bash
# Run tests locally first
cd backend
php artisan test
```

#### 2. **SonarQube Scan Fails**
- Verify `SONAR_TOKEN` and `SONAR_HOST_URL` secrets are set correctly
- Check SonarQube server is accessible
- Ensure `sonar-project.properties` is properly configured

#### 3. **Docker Push Fails**
- Verify Docker Hub credentials are correct
- Ensure the repository exists on Docker Hub
- Check Docker Hub is accessible

#### 4. **Database Connection Issues**
- MySQL service may not be ready
- Check health check configuration
- Review database credentials in workflow

#### 5. **Frontend Build Fails**
- Ensure `package.json` and `package-lock.json` are committed
- Check Node.js version compatibility
- Review npm build script

---

## Pipeline Optimization Tips

### 1. **Cache Dependencies**
Add caching for faster builds:
```yaml
- name: Cache Composer dependencies
  uses: actions/cache@v3
  with:
    path: backend/vendor
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
```

### 2. **Parallel Jobs**
Split testing and code quality into separate jobs:
```yaml
jobs:
  test:
    # Testing steps
  
  quality:
    # SonarQube scan
  
  deploy:
    needs: [test, quality]
    # Docker build and push
```

### 3. **Environment-Specific Deployments**
Deploy to staging and production separately:
```yaml
on:
  push:
    branches:
      - main      # Production
      - develop   # Staging
```

---

## Security Best Practices

1. **Never commit secrets** to the repository
2. **Use GitHub Secrets** for all sensitive data
3. **Rotate tokens** regularly (Docker, SonarQube)
4. **Review permissions** for access tokens
5. **Enable branch protection** rules for `main`

---

## Additional Resources

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Testing Guide](https://laravel.com/docs/10.x/testing)
- [SonarQube Documentation](https://docs.sonarqube.org/)
- [Docker Hub Documentation](https://docs.docker.com/docker-hub/)

---

## Support

For issues or questions:
1. Check the GitHub Actions logs
2. Review this documentation
3. Open an issue in the repository
4. Contact the DevOps team

---

**Last Updated:** 2025-10-23
**Pipeline Version:** 1.0
**Maintained by:** TounsiVert Development Team
