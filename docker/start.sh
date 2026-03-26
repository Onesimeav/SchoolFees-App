#!/bin/sh
set -e

echo "==> Installing composer dependencies..."
composer install --no-dev --working-dir=/var/www/html --optimize-autoloader --no-interaction

echo "==> Clearing stale cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Caching config, routes and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Linking storage..."
php artisan storage:link --force

PORT=${PORT:-10000}
echo "==> Generating nginx config on port ${PORT}..."
envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template \
    > /etc/nginx/http.d/default.conf

echo "==> Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
