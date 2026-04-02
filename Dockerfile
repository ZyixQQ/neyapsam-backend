FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash curl git unzip \
    icu-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    libzip-dev oniguruma-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql mbstring exif pcntl bcmath gd zip opcache intl

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/backend

COPY . .

RUN if [ -f composer.json ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction; \
    fi \
    && chown -R www-data:www-data /var/www/backend \
    && chmod -R 755 /var/www/backend/storage 2>/dev/null || true

EXPOSE 9000
CMD ["php-fpm"]
