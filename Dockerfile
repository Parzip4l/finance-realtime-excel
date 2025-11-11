# Gunakan PHP + Apache
FROM php:8.2-apache

# Install dependensi sistem & ekstensi PHP
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy seluruh project
COPY . .

# Aktifkan rewrite Apache
RUN a2enmod rewrite

# Expose port Laravel
EXPOSE 6728

# Jalankan Apache
CMD ["apache2-foreground"]
