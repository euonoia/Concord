
FROM php:8.5-apache

WORKDIR /var/www/html

COPY package*.json ./
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

COPY resources/ ./resources/


RUN npm install

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

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


COPY . .


RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache


EXPOSE 80

CMD ["apache2-foreground"]
