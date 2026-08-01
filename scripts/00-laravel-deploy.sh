#!/usr/bin/env bash
set -e

echo "Discovering packages..."
php artisan package:discover --ansi

echo "Running migrations..."
php artisan migrate --force

echo "Seeding demo data..."
php artisan db:seed --force

echo "Linking storage..."
php artisan storage:link || true

echo "Caching Laravel..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

echo "Deploy script finished."
