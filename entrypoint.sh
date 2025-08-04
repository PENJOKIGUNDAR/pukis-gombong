#!/bin/sh

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Run migration langsung
echo "🚀 Jalankan migration..."
php artisan migrate --force --no-interaction

# (Optional) cache config & route kalau production
echo "⚙️ Optimize Laravel..."
php artisan config:cache
php artisan route:cache

# Start PHP-FPM
echo "✅ Siap terima request!"
php artisan serve --host=0.0.0.0 --port=8080