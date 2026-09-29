# syntax=docker/dockerfile:1
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN npm run build

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader --ignore-platform-req=ext-gd
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction

FROM php:8.4-fpm-alpine AS app
RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype sqlite-libs libpq su-exec \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev sqlite-dev postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath gd intl opcache pcntl pdo_mysql pdo_pgsql pdo_sqlite zip \
    && apk del .build-deps
WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=frontend --chown=www-data:www-data /app/public/build /var/www/html/public/build
COPY docker/php.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/entrypoint.sh /usr/local/bin/esdep-entrypoint
RUN chmod +x /usr/local/bin/esdep-entrypoint \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs database \
    && chown -R www-data:www-data storage bootstrap/cache database
ENTRYPOINT ["esdep-entrypoint"]
CMD ["php-fpm", "-F"]

FROM nginx:1.27-alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
