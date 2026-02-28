FROM php:8.4-fpm

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    libicu-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory permissions
RUN chown -R www-data:www-data /var/www/html

# Change current user to www-data
USER www-data

# Expose port 8000 and start php-fpm server
EXPOSE 8000

# Start command that waits for composer dependencies
CMD sh -c "if [ ! -f vendor/autoload.php ]; then echo '⚠️  Les dépendances Composer ne sont pas installées. Exécutez: docker-compose exec app composer install'; sleep 3600; else php artisan serve --host=0.0.0.0 --port=8000; fi"
