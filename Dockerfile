# Development Dockerfile for Laravel 12
# Runs: php artisan serve

FROM php:8.2-cli

# System deps
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo_mysql zip mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www/html

# Copy composer files first (better caching)
COPY composer.json composer.lock ./

# Install PHP deps
RUN composer install --no-interaction --prefer-dist

# Copy the rest of the app
COPY . .

# Ensure writable dirs
RUN mkdir -p storage/framework storage/logs \
  && chmod -R 777 storage bootstrap/cache

# Expose Laravel dev server port
EXPOSE 8000

# Default command (can be overridden in docker-compose)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

