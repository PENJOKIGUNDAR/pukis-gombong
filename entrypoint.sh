#!/bin/sh

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Tunggu database siap (opsional, tapi aman)
echo "⏳ Nunggu database nyala..."

until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
    } catch (PDOException \$e) {
        exit(1);
    }
" >/dev/null 2>&1; do
    echo '❌ DB belum siap...'
    sleep 5
done

echo "✅ Database siap!"

# Run migration
php artisan migrate --force

# Start PHP-FPM
php-fpm