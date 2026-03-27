#!/bin/sh
set -e

echo "→ Running Laravel bootstrap tasks..."

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force 2>/dev/null || true

echo "→ Running migrations..."
php artisan migrate --force

echo "→ Starting application..."
exec "$@"
