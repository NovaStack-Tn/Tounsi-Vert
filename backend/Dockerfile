# Production Dockerfile for Laravel 10 TounsiVert Application
# Note: This container runs PHP-FPM on port 9000. An Nginx reverse proxy is required
# to serve static files and proxy PHP requests (handle via docker-compose or platform).

FROM php:8.3-fpm-alpine
WORKDIR /var/www/html

# System libs + PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \        
    libxml2-dev \
    zip \
    unzip \
    mysql-client \
    icu-dev \
    oniguruma-dev \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install -j"$(nproc)" \
       pdo_mysql mbstring gd xml intl opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app + install prod deps
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
