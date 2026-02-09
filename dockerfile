# STAGE 1: Node.js to build assets
FROM node:20 AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .

# SAFETY NET: Remove old configs that crash Tailwind v4 builds
RUN rm -f tailwind.config.js postcss.config.js postcss.config.cjs

RUN npm run build

# STAGE 2: PHP & Apache
FROM php:8.4-apache
WORKDIR /var/www/html

# CHANGE: Swapped libpq-dev for libmariadb-dev and added pdo_mysql
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy code and the built assets
COPY . .
COPY --from=asset-builder /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Ensure the certs directory exists for TiDB SSL
RUN mkdir -p storage/app/certs

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache
    
RUN rm -f public/hot

EXPOSE 80
CMD ["apache2-foreground"]