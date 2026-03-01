# PHP + Apache for Render Free (skip Node build)
FROM php:8.4-apache
WORKDIR /var/www/html

# Install PHP system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Apache setup
RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy all code including prebuilt assets
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN mkdir -p storage/app/certs && chown -R www-data:www-data storage/app/certs

# Remove hot reload file if exists
RUN rm -f public/hot

EXPOSE 80

# Run Laravel commands + start Apache
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"