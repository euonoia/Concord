# --------------------------
# Stage 1: Build Vite assets
# --------------------------
FROM node:20-alpine AS vite-build

WORKDIR /app

# Copy package files first (better caching)
COPY package.json package-lock.json* ./
RUN npm install

# Copy the rest of the project
COPY . .

# Build Vite assets
RUN npm run build


# --------------------------
# Stage 2: PHP + Apache
# --------------------------
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
RUN a2enmod rewrite headers \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# --------------------------
# Install Composer
# --------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --------------------------
# Copy Laravel application
# --------------------------
COPY . .

# --------------------------
# Copy built Vite assets from Node stage
# --------------------------
COPY --from=vite-build /app/public/build public/build

# --------------------------
# Install PHP dependencies
# --------------------------
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# --------------------------
# Set proper permissions
# --------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# --------------------------
# Remove Laravel hot reload file
# --------------------------
RUN rm -f public/hot || true

# --------------------------
# Expose port
# --------------------------
EXPOSE 80

# --------------------------
# Run Laravel + Apache
# --------------------------
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"