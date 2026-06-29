FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libonig-dev \
    libsqlite3-dev \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# SQLite voor dev, MySQL voor als je later switcht
RUN docker-php-ext-install pdo_sqlite pdo_mysql zip mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN composer run-script post-autoload-dump || true

# Assets builden voor productie, of weglaten als je Vite dev server apart draait
RUN npm run build

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]