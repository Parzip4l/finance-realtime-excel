# Gunakan image PHP 8.2 dengan Apache
FROM php:8.2-apache

# Install dependency yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy source code ke container
COPY . /var/www/html

# Set permission folder penting Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Aktifkan mod_rewrite untuk Laravel route
RUN a2enmod rewrite

# Copy konfigurasi Apache khusus Laravel
COPY ./docker/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# Expose port 6728
EXPOSE 6728

# Ganti default Apache port ke 6728
RUN sed -i 's/80/6728/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

# Jalankan Apache
CMD ["apache2-foreground"]
