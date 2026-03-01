# PHP + Apache for Render Free (skip Node build)
FROM php:8.4-apache
WORKDIR /var/www/html

# --------------------------
# Install PHP system dependencies
# --------------------------
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# --------------------------
# Apache setup
# --------------------------
RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# --------------------------
# Install Composer
# --------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------
# Copy application code
# --------------------------
COPY . .

# --------------------------
# Ensure prebuilt Vite assets exist in public/build
# --------------------------
# This step assumes you committed local build files into public/build
# If your assets are in a different folder, adjust the path below
RUN mkdir -p public/build \
    && cp -R build/* public/build/ || echo "No build folder found, ensure prebuilt assets exist"

# --------------------------
# Install PHP dependencies
# --------------------------
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --------------------------
# Set proper permissions for Laravel
# --------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p storage/app/certs && chown -R www-data:www-data storage/app/certs

# --------------------------
# Remove Laravel hot reload file if exists
# --------------------------
RUN rm -f public/hot

# --------------------------
# Expose HTTP port
# --------------------------
EXPOSE 80

# --------------------------
# Run artisan commands + start Apache
# --------------------------
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"