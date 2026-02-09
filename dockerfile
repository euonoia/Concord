# Stage 1: Build Assets
FROM node:20 AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Production Server
FROM php:8.5-apache

WORKDIR /var/www/html

# ... (Your existing apt-get and php-ext-install commands) ...
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy the application
COPY . .

# IMPORTANT: Copy the compiled assets from the first stage
COPY --from=asset-builder /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]