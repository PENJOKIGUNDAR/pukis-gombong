#!/bin/sh

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Jalankan migration
echo "🚀 Jalankan migration..."
php artisan migrate --force --no-interaction

# Jalankan seeder
echo "🌱 Jalankan seeder..."
php artisan db:seed --force

# (Optional) cache config & route kalau production
echo "⚙️ Optimize Laravel..."
php artisan config:cache
php artisan route:cache

# Start PHP built-in server
echo "✅ Siap terima request!"
php artisan serve --host=0.0.0.0 --port=8080
