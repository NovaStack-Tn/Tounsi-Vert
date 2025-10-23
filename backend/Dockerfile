# Production Dockerfile for Laravel 10 TounsiVert Application
# Note: This container runs PHP-FPM on port 9000. An Nginx reverse proxy is required
# to serve static files and proxy PHP requests (handle via docker-compose or platform).

FROM php:8.3-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libxml2-dev \
    zip \
    unzip \
    mysql-client \
    icu-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    gd \
    xml \
    intl \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose PHP-FPM port
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
