#!/bin/sh

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Tunggu database siap (opsional, tapi aman)
echo "⏳ Nunggu database nyala..."
until nc -z -v -w30 "$DB_HOST" "$DB_PORT"
do
  echo "❌ DB belum siap di $DB_HOST:$DB_PORT"
  sleep 5
done
echo "✅ Database siap!"

# Run migration
php artisan migrate --force

# Start PHP-FPM
php-fpm