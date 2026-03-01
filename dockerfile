# PHP + Apache (assets already committed)
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
# Apache configuration
# --------------------------
RUN a2enmod rewrite headers \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# --------------------------
# Install Composer
# --------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------
# Copy application (includes public/build)
# --------------------------
COPY . .

# --------------------------
# Install PHP dependencies
# --------------------------
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --------------------------
# Ensure required directories exist
# --------------------------
RUN mkdir -p storage/app/certs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# --------------------------
# Remove Vite hot reload file (if exists)
# --------------------------
RUN rm -f public/hot || true

# --------------------------
# Expose HTTP port
# --------------------------
EXPOSE 80

# --------------------------
# Start Apache only (safer for Render)
# --------------------------
CMD apache2-foreground