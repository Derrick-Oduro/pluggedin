FROM php:8.4-cli-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    APP_DIR=/var/www/html

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y --no-install-recommends \
        curl \
        git \
        gnupg \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-install bcmath mbstring pdo pdo_pgsql zip gd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
# Install PHP dependencies but skip composer scripts until app files are present
RUN composer install --no-dev --prefer-dist --no-progress --optimize-autoloader --no-scripts

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Now that application files (including artisan) exist, run composer scripts that require them
RUN composer dump-autoload --optimize || true \
    && php artisan package:discover --ansi || true

RUN npm run build \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

CMD ["bash", "docker/start.sh"]
