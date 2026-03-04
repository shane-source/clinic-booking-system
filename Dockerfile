FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate app key
RUN php artisan key:generate

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000