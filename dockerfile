# STAGE 1: Node.js to build assets
FROM node:20 AS asset-builder
WORKDIR /app
COPY package*.json ./
RUN npm install --legacy-peer-deps
RUN apt-get update && apt-get install -y python3 g++ make build-essential
COPY . .
RUN npm run build
RUN rm -f tailwind.config.js postcss.config.js postcss.config.cjs

# STAGE 2: PHP & Apache
FROM php:8.4-apache
WORKDIR /var/www/html
RUN apt-get update && apt-get install -y git curl zip unzip libpng-dev libonig-dev libxml2-dev libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY . .
COPY --from=asset-builder /app/public/build ./public/build
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
RUN mkdir -p storage/app/certs && chown -R www-data:www-data storage/app/certs
RUN rm -f public/hot

EXPOSE 80
CMD sh -c "php artisan config:clear && php artisan migrate --force && php artisan optimize && apache2-foreground"