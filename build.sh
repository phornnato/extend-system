#!/bin/bash
set -e

echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Clearing cache..."
php artisan config:cache
php artisan route:cache

echo "Build complete!"
