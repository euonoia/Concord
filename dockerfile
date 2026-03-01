# STAGE 1: Node.js to build assets
FROM node:20 AS asset-builder
WORKDIR /app

# Copy package.json and lock file if exists
COPY package*.json ./

# Clean npm install (legacy-peer-deps to avoid conflicts)
RUN npm install --legacy-peer-deps

# Install build tools required for native Node modules
RUN apt-get update && apt-get install -y python3 g++ make build-essential

# Copy the rest of the source code
COPY . .

# Build assets (configs must exist during build)
RUN npm run build

# Optional: remove Tailwind/PostCSS configs after build
RUN rm -f tailwind.config.js postcss.config.js postcss.config.cjs

# STAGE 2: PHP & Apache
FROM php:8.4-apache
WORKDIR /var/www/html

# Install system dependencies for PHP + MySQL/TiDB
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules and update DocumentRoot
RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application code and built assets from Stage 1
COPY . .
COPY --from=asset-builder /app/public/build ./public/build

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Set permissions for Laravel storage/bootstrap
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# TiDB SSL often requires writable storage path
RUN mkdir -p storage/app/certs && chown -R www-data:www-data storage/app/certs

# Remove Laravel hot reload file if exists
RUN rm -f public/hot

# Expose HTTP port
EXPOSE 80

# Run Laravel artisan commands and start Apache
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"