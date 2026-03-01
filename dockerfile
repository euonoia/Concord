# STAGE 1: Node.js to build assets
FROM node:20 AS asset-builder
WORKDIR /app

# Copy npm dependency files
COPY package*.json ./

# Install Node dependencies
RUN npm install --legacy-peer-deps

# Install build tools for native modules
RUN apt-get update && apt-get install -y python3 g++ make build-essential

# Copy app source code
COPY . .

# Build Vite/Tailwind assets
RUN npm run build

# Optional: remove configs after build
RUN rm -f tailwind.config.js postcss.config.js postcss.config.cjs

# STAGE 2: PHP & Apache
FROM php:8.4-apache
WORKDIR /var/www/html

# Install system dependencies for PHP + MySQL/TiDB
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Apache configuration
RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app code and built assets
COPY . .
COPY --from=asset-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# TiDB cert path
RUN mkdir -p storage/app/certs && chown -R www-data:www-data storage/app/certs

# Remove hot reload file
RUN rm -f public/hot

# Expose HTTP port
EXPOSE 80

# Run Laravel commands + Apache
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"