#!/bin/sh
set -eu

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs database
chown -R www-data:www-data storage bootstrap/cache database

if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] && [ ! -f "${DB_DATABASE:-/var/www/html/database/database.sqlite}" ]; then
    touch "${DB_DATABASE:-/var/www/html/database/database.sqlite}"
fi

if [ "${AUTO_MIGRATE:-false}" = "true" ]; then
    su-exec www-data php artisan migrate --force
fi

su-exec www-data php artisan config:cache
su-exec www-data php artisan view:cache

exec su-exec www-data "$@"
