#!/usr/bin/env sh
set -e

echo "Starting Laravel runtime..."
php artisan migrate --force
php artisan db:seed --class=WoodScalingSeeder --force
php artisan config:clear
php artisan config:cache

echo "Listening on 0.0.0.0:${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
