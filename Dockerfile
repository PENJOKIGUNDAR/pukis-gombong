# Stage 1: Build assets with Node
FROM node:18 as frontend

WORKDIR /app

COPY package.json vite.config.js ./
COPY resources ./resources

RUN npm install
RUN npm run build

# Stage 2: PHP & Laravel
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application
COPY . .

# Copy built assets from Node stage
COPY --from=frontend /app/public ./public

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage /var/www/bootstrap/cache   

# Expose port 9000
EXPOSE 9000     

# Start php-fpm server
CMD ["./entrypoint.sh"]
