
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

COPY . .

FROM node:20 AS frontend

WORKDIR /app
COPY package*.json ./

RUN npm install
COPY . .
RUN npm run build || echo "Skip frontend build (no Vite/Mix found)"

FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    bash \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    libfreetype-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html

COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public /var/www/html/public

COPY .docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY .docker/supervisord.conf /etc/supervisord.conf

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 6728 
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
