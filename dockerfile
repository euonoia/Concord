# 1️⃣ Build stage
FROM node:20-alpine AS build

WORKDIR /var/www/html

# Copy package files
COPY package*.json ./
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

# Install npm dependencies
RUN npm install

# Copy all resources for Vite build
COPY resources/ ./resources/

# Build production assets
RUN npm run build

# 2️⃣ Production stage
FROM php:8.5-apache

WORKDIR /var/www/html

# Install PHP dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# Copy PHP app
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .

# Copy built assets from Node stage
COPY --from=build /var/www/html/public/build ./public/build

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]
