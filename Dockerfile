# ---- 1. PHP dependencies -----------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --no-autoloader --ignore-platform-reqs

# ---- 2. Front-end assets ------------------------------------------------------
FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.cjs ./
# Tailwind also scans the paginator views for class names.
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

# ---- 3. PHP application served by FrankenPHP -------------------------------
# FrankenPHP is a production PHP server (Caddy-based) that serves many requests
# concurrently, unlike `php artisan serve`, which handles one at a time.
FROM dunglas/frankenphp:1-php8.4-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    SERVER_NAME=":80"

# Must stay /var/www/html: render.yaml mounts the persistent uploads disk at /var/www/html/storage.
WORKDIR /var/www/html

RUN install-php-extensions bcmath exif gd intl mbstring opcache pcntl pdo_pgsql zip \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && { \
        echo 'upload_max_filesize=25M'; \
        echo 'post_max_size=30M'; \
        echo 'memory_limit=256M'; \
        echo 'expose_php=Off'; \
        echo 'opcache.enable=1'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.memory_consumption=128'; \
    } > "$PHP_INI_DIR/conf.d/zz-pluggedin.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN rm -f public/hot \
    && mkdir -p storage/framework/cache storage/framework/views bootstrap/cache \
    && composer dump-autoload --optimize \
    && (php artisan package:discover --ansi || true) \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

CMD ["bash", "docker/start.sh"]
