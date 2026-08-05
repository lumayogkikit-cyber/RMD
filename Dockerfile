# Railway-compatible Dockerfile for Laravel 12 with Vite asset build
FROM php:8.2-fpm-alpine

RUN apk add --no-cache git curl nodejs npm icu-dev libzip-dev libxml2-dev oniguruma-dev zlib-dev shadow bash postgresql-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath xml zip intl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY package.json package-lock.json ./
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
    && npm ci \
    && npm run build
RUN cp .env.example .env && php artisan key:generate --force

EXPOSE 8000
CMD ["sh", "-lc", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
