# 1️⃣ Node builder stage
FROM node:20-alpine AS node-builder
WORKDIR /var/www/html

# Copy package.json and package-lock.json first
COPY package*.json ./

# Install Node deps
RUN npm install

# Copy all project files
COPY . .

# Build assets
RUN npm run build

# 2️⃣ PHP/Apache production stage
FROM php:8.5-apache

WORKDIR /var/www/html

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Copy built assets from Node builder
COPY --from=node-builder /var/www/html/public/build ./public/build

# Copy rest of Laravel app
COPY --from=node-builder /var/www/html ./

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
